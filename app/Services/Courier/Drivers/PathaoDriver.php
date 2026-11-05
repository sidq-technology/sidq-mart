<?php

namespace App\Services\Courier\Drivers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\Courier\CourierInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoDriver implements CourierInterface
{
    protected string $baseUrl = 'https://api-hermes.pathao.com';

    public function getName(): string
    {
        return 'pathao';
    }

    public function getLabel(): string
    {
        return 'Pathao Courier (পাঠাও)';
    }

    public function isEnabled(): bool
    {
        $enabled = Setting::get('courier_pathao_enabled', '0') == '1';
        $clientId = Setting::get('courier_pathao_client_id');
        $clientSecret = Setting::get('courier_pathao_client_secret');
        $username = Setting::get('courier_pathao_username');
        $password = Setting::get('courier_pathao_password');

        return $enabled && !empty($clientId) && !empty($clientSecret) && !empty($username) && !empty($password);
    }

    public function getAccessToken(bool $forceRefresh = false): ?string
    {
        $cacheKey = 'pathao_courier_access_token';
        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = Setting::get('courier_pathao_client_id');
        $clientSecret = Setting::get('courier_pathao_client_secret');
        $username = Setting::get('courier_pathao_username');
        $password = Setting::get('courier_pathao_password');

        if (empty($clientId) || empty($clientSecret) || empty($username) || empty($password)) {
            throw new \Exception('Pathao API ক্রেডেনশিয়াল অসম্পূর্ণ। Client ID, Secret, Username ও Password প্রয়োজন।');
        }

        $response = Http::timeout(15)->post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password',
        ]);

        $data = $response->json();
        if ($response->successful() && !empty($data['access_token'])) {
            $token = $data['access_token'];
            $expiresIn = max(60, ($data['expires_in'] ?? 604800) - 3600);
            Cache::put($cacheKey, $token, $expiresIn);
            return $token;
        }

        $err = $data['message'] ?? ($data['error'] ?? 'Pathao Token Request Failed');
        throw new \Exception('Pathao লগইন ব্যর্থ: ' . (is_array($err) ? json_encode($err) : $err));
    }

    public function testCredentials(): array
    {
        try {
            $token = $this->getAccessToken(true);
            return [
                'success' => true,
                'message' => 'Pathao এপিআই সফলভাবে সংযুক্ত হয়েছে! অ্যাক্সেস টোকেন প্রাপ্ত হয়েছে।'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendOrder(Order $order): array
    {
        try {
            $token = $this->getAccessToken();
            $storeId = (int) Setting::get('courier_pathao_store_id', '0');

            // If storeId is not set, try to auto-fetch first active store
            if ($storeId <= 0) {
                try {
                    $storesRes = Http::timeout(10)->withToken($token)->acceptJson()->get("{$this->baseUrl}/aladdin/api/v1/stores");
                    $storesData = $storesRes->json();
                    if (!empty($storesData['data']['data'][0]['store_id'])) {
                        $storeId = (int) $storesData['data']['data'][0]['store_id'];
                    }
                } catch (\Throwable $e) {
                    // Ignore and fallback
                }
            }

            $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
            if (str_starts_with($cleanPhone, '880')) {
                $cleanPhone = substr($cleanPhone, 2);
            }

            $codAmount = (int) round($order->payment_method === 'cod' ? $order->grand_total : 0);

            // Pathao requires delivery_type (48: Normal, 12: On Demand) and item_type (1: Document, 2: Parcel)
            $payload = [
                'store_id' => $storeId > 0 ? $storeId : 1,
                'merchant_order_id' => $order->order_number,
                'recipient_name' => $order->customer_name,
                'recipient_phone' => $cleanPhone,
                'recipient_address' => $order->shipping_address,
                'recipient_city' => $order->delivery_zone === 'inside_dhaka' ? 1 : 2,
                'recipient_zone' => 1,
                'delivery_type' => 48,
                'amount_to_collect' => $codAmount,
                'item_type' => 2,
                'item_quantity' => $order->items()->sum('quantity') ?: 1,
                'item_weight' => 0.5,
                'item_description' => 'E-Commerce Package',
                'special_instruction' => $order->admin_notes ?: ($order->customer_note ?: ''),
            ];

            $response = Http::timeout(20)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/aladdin/api/v1/orders", $payload);

            $data = $response->json();

            if ($response->successful() && !empty($data['data']['consignment_id'])) {
                $consignmentId = $data['data']['consignment_id'];
                $trackingCode = $data['data']['consignment_id'];

                return [
                    'success' => true,
                    'courier' => 'pathao',
                    'tracking_code' => $trackingCode,
                    'consignment_id' => $consignmentId,
                    'status' => 'booked',
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে Pathao-তে বুকিং হয়েছে! কনসাইনমেন্ট আইডি: {$consignmentId}"
                ];
            }

            $errorMsg = 'Pathao থেকে ত্রুটি এসেছে';
            if (!empty($data['errors']) && is_array($data['errors'])) {
                $details = [];
                foreach ($data['errors'] as $field => $messages) {
                    $m = is_array($messages) ? implode(', ', $messages) : $messages;
                    $details[] = "{$field}: {$m}";
                }
                $errorMsg = implode('; ', $details);
            } elseif (!empty($data['message'])) {
                $errorMsg = $data['message'];
            }

            return [
                'success' => false,
                'message' => 'Pathao ত্রুটি: ' . $errorMsg
            ];
        } catch (\Throwable $e) {
            Log::error('Pathao sendOrder exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Pathao রিকোয়েস্ট ব্যর্থ হয়েছে: ' . $e->getMessage()
            ];
        }
    }
}
