<?php

namespace App\Services\Courier\Drivers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\Courier\CourierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaperflyDriver implements CourierInterface
{
    protected string $baseUrl = 'https://api.paperfly.com.bd';

    public function getName(): string
    {
        return 'paperfly';
    }

    public function getLabel(): string
    {
        return 'Paperfly Courier (পেপারফ্লাই)';
    }

    public function isEnabled(): bool
    {
        $enabled = Setting::get('courier_paperfly_enabled', '0') == '1';
        $user = Setting::get('courier_paperfly_username');
        $pass = Setting::get('courier_paperfly_password');
        $key = Setting::get('courier_paperfly_key');

        return $enabled && !empty($user) && !empty($pass) && !empty($key);
    }

    public function testCredentials(): array
    {
        $user = Setting::get('courier_paperfly_username');
        $pass = Setting::get('courier_paperfly_password');
        $key = Setting::get('courier_paperfly_key');

        if (empty($user) || empty($pass) || empty($key)) {
            return [
                'success' => false,
                'message' => 'Paperfly Username, Password অথবা Key অসম্পূর্ণ।'
            ];
        }

        try {
            // Check credentials by calling Paperfly tracking or test endpoint
            $response = Http::timeout(15)
                ->withBasicAuth($user, $pass)
                ->withHeaders([
                    'paperflykey' => $key,
                    'Accept' => 'application/json',
                ])
                ->post("{$this->baseUrl}/Tracking", ['ReferenceNumber' => 'TEST_CHECK']);

            if ($response->status() !== 401 && $response->status() !== 403) {
                return [
                    'success' => true,
                    'message' => 'Paperfly এপিআই ক্রেডেনশিয়াল সফলভাবে যাচাই করা হয়েছে!'
                ];
            }

            return [
                'success' => false,
                'message' => 'Paperfly অথেন্টিকেশন ব্যর্থ: ইউজারনেম, পাসওয়ার্ড বা কি ভুল রয়েছে।'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Paperfly সার্ভার কানেকশন সমস্যা: ' . $e->getMessage()
            ];
        }
    }

    public function sendOrder(Order $order): array
    {
        $user = Setting::get('courier_paperfly_username');
        $pass = Setting::get('courier_paperfly_password');
        $key = Setting::get('courier_paperfly_key');

        if (empty($user) || empty($pass) || empty($key)) {
            return [
                'success' => false,
                'message' => 'Paperfly ক্রেডেনশিয়াল কনফিগার করা হয়নি।'
            ];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($cleanPhone, '880')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        $codAmount = (int) round($order->payment_method === 'cod' ? $order->grand_total : 0);

        $payload = [
            'merOrderRef' => $order->order_number,
            'packagePrice' => $codAmount,
            'packageDescription' => 'E-commerce goods',
            'custName' => $order->customer_name,
            'custPhone' => $cleanPhone,
            'custAddress' => $order->shipping_address,
            'remarks' => $order->customer_note ?: '',
        ];

        try {
            $response = Http::timeout(20)
                ->withBasicAuth($user, $pass)
                ->withHeaders([
                    'paperflykey' => $key,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->baseUrl}/OrderPlacement", $payload);

            $data = $response->json();

            if ($response->successful() && !empty($data['success']['tracking_number'])) {
                $trackingCode = $data['success']['tracking_number'];

                return [
                    'success' => true,
                    'courier' => 'paperfly',
                    'tracking_code' => $trackingCode,
                    'consignment_id' => $trackingCode,
                    'status' => 'placed',
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে Paperfly-তে বুকিং হয়েছে! ট্র্যাকিং নম্বর: {$trackingCode}"
                ];
            }

            $errorMsg = $data['message'] ?? ($data['error'] ?? 'Paperfly থেকে ত্রুটি এসেছে');
            if (is_array($errorMsg)) {
                $errorMsg = json_encode($errorMsg);
            }

            return [
                'success' => false,
                'message' => 'Paperfly ত্রুটি: ' . $errorMsg
            ];
        } catch (\Throwable $e) {
            Log::error('Paperfly sendOrder exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Paperfly রিকোয়েস্ট ব্যর্থ হয়েছে: ' . $e->getMessage()
            ];
        }
    }
}
