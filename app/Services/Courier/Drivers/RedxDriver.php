<?php

namespace App\Services\Courier\Drivers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\Courier\CourierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedxDriver implements CourierInterface
{
    protected string $baseUrl = 'https://openapi.redx.com.bd/v1.0.0-beta';

    public function getName(): string
    {
        return 'redx';
    }

    public function getLabel(): string
    {
        return 'RedX Courier (রেডএক্স)';
    }

    public function isEnabled(): bool
    {
        $enabled = Setting::get('courier_redx_enabled', '0') == '1';
        $token = Setting::get('courier_redx_access_token');

        return $enabled && !empty($token);
    }

    public function testCredentials(): array
    {
        $token = Setting::get('courier_redx_access_token');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'RedX Access Token খালি রয়েছে।'
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/pickup_stores");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'RedX এপিআই সফলভাবে সংযুক্ত হয়েছে! পিকআপ স্টোর পাওয়া গেছে।'
                ];
            }

            $data = $response->json();
            $msg = $data['message'] ?? 'RedX টোকেন অবৈধ বা মেয়াদোত্তীর্ণ।';

            return [
                'success' => false,
                'message' => 'RedX ত্রুটি: ' . $msg
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'RedX সার্ভার কানেকশন ত্রুটি: ' . $e->getMessage()
            ];
        }
    }

    public function sendOrder(Order $order): array
    {
        $token = Setting::get('courier_redx_access_token');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'RedX Access Token কনফিগার করা হয়নি।'
            ];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($cleanPhone, '880')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        $codAmount = (int) round($order->payment_method === 'cod' ? $order->grand_total : 0);

        $payload = [
            'customer_name' => $order->customer_name,
            'customer_phone' => $cleanPhone,
            'delivery_area' => $order->delivery_zone === 'inside_dhaka' ? 'Dhaka' : 'Outside Dhaka',
            'customer_address' => $order->shipping_address,
            'merchant_invoice_id' => $order->order_number,
            'cash_collection_amount' => $codAmount,
            'parcel_weight' => 500,
            'instruction' => $order->admin_notes ?: ($order->customer_note ?: ''),
        ];

        try {
            $response = Http::timeout(20)
                ->withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/parcels", $payload);

            $data = $response->json();

            if ($response->successful() && !empty($data['tracking_id'])) {
                $trackingCode = $data['tracking_id'];

                return [
                    'success' => true,
                    'courier' => 'redx',
                    'tracking_code' => $trackingCode,
                    'consignment_id' => $trackingCode,
                    'status' => 'created',
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে RedX-এ বুকিং হয়েছে! ট্র্যাকিং কোড: {$trackingCode}"
                ];
            }

            $errorMsg = $data['message'] ?? ($data['errors'] ?? 'RedX থেকে ত্রুটি এসেছে');
            if (is_array($errorMsg)) {
                $errorMsg = json_encode($errorMsg);
            }

            return [
                'success' => false,
                'message' => 'RedX ত্রুটি: ' . $errorMsg
            ];
        } catch (\Throwable $e) {
            Log::error('RedX sendOrder exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'RedX রিকোয়েস্ট ব্যর্থ হয়েছে: ' . $e->getMessage()
            ];
        }
    }
}
