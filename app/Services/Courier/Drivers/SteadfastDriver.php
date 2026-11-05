<?php

namespace App\Services\Courier\Drivers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\Courier\CourierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastDriver implements CourierInterface
{
    protected string $baseUrl = 'https://portal.packzy.com/api/v1';

    public function getName(): string
    {
        return 'steadfast';
    }

    public function getLabel(): string
    {
        return 'Steadfast Courier (স্টেডফাস্ট)';
    }

    public function isEnabled(): bool
    {
        $enabled = Setting::get('courier_steadfast_enabled', '0') == '1';
        $apiKey = Setting::get('courier_steadfast_api_key');
        $secretKey = Setting::get('courier_steadfast_secret_key');

        return $enabled && !empty($apiKey) && !empty($secretKey);
    }

    public function testCredentials(): array
    {
        $apiKey = Setting::get('courier_steadfast_api_key');
        $secretKey = Setting::get('courier_steadfast_secret_key');

        if (empty($apiKey) || empty($secretKey)) {
            return [
                'success' => false,
                'message' => 'Steadfast API Key অথবা Secret Key খালি রয়েছে।'
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Api-Key' => $apiKey,
                    'Secret-Key' => $secretKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/get_balance");

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] == 200) {
                $balance = $data['current_balance'] ?? 0;
                return [
                    'success' => true,
                    'message' => "Steadfast এপিআই সফলভাবে সংযুক্ত হয়েছে! বর্তমান একাউন্ট ব্যালেন্স: ৳{$balance}"
                ];
            }

            $errorMsg = $data['message'] ?? ($data['errors'] ?? 'অবৈধ ক্রেডেনশিয়াল বা অথেন্টিকেশন ত্রুটি');
            return [
                'success' => false,
                'message' => 'Steadfast ত্রুটি: ' . (is_array($errorMsg) ? json_encode($errorMsg) : $errorMsg)
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Steadfast সার্ভারের সাথে যোগাযোগ করা যায়নি: ' . $e->getMessage()
            ];
        }
    }

    public function sendOrder(Order $order): array
    {
        $apiKey = Setting::get('courier_steadfast_api_key');
        $secretKey = Setting::get('courier_steadfast_secret_key');

        if (empty($apiKey) || empty($secretKey)) {
            return [
                'success' => false,
                'message' => 'Steadfast API Key অথবা Secret Key কনফিগার করা হয়নি।'
            ];
        }

        // Clean Bangladeshi phone number (ensure 11 digits, strip +88)
        $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($cleanPhone, '880')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        $codAmount = (int) round($order->payment_method === 'cod' ? $order->grand_total : 0);

        $payload = [
            'invoice' => $order->order_number,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $cleanPhone,
            'recipient_address' => $order->shipping_address,
            'cod_amount' => $codAmount,
            'note' => $order->admin_notes ?: ($order->customer_note ?: 'Handle with care'),
        ];

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Api-Key' => $apiKey,
                    'Secret-Key' => $secretKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->baseUrl}/create_order", $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] == 200) {
                $consignment = $data['consignment'] ?? [];
                $trackingCode = $consignment['tracking_code'] ?? ($consignment['consignment_id'] ?? '');
                $consignmentId = (string) ($consignment['consignment_id'] ?? '');

                return [
                    'success' => true,
                    'courier' => 'steadfast',
                    'tracking_code' => $trackingCode,
                    'consignment_id' => $consignmentId,
                    'status' => $consignment['status'] ?? 'in_review',
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে Steadfast-এ বুকিং হয়েছে! ট্র্যাকিং কোড: {$trackingCode}"
                ];
            }

            $errorMsg = $data['message'] ?? ($data['errors'] ?? 'Steadfast থেকে ত্রুটি এসেছে');
            if (is_array($errorMsg)) {
                $errorMsg = implode(', ', array_map(fn($v) => is_array($v) ? implode(' ', $v) : $v, $errorMsg));
            }

            return [
                'success' => false,
                'message' => 'Steadfast ত্রুটি: ' . $errorMsg
            ];
        } catch (\Throwable $e) {
            Log::error('Steadfast sendOrder exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Steadfast রিকোয়েস্ট ব্যর্থ হয়েছে: ' . $e->getMessage()
            ];
        }
    }
}
