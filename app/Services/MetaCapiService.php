<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    protected string $graphApiVersion = 'v19.0';
    protected string $baseUrl = 'https://graph.facebook.com';

    /**
     * Check if Meta CAPI is enabled and properly configured
     */
    public function isEnabled(): bool
    {
        $enabled = Setting::get('meta_pixel_enabled', '1') == '1';
        $pixelId = Setting::get('meta_pixel_id');
        $token = Setting::get('meta_capi_token');

        return $enabled && !empty($pixelId) && !empty($token);
    }

    /**
     * Get configured Pixel ID
     */
    public function getPixelId(): ?string
    {
        return Setting::get('meta_pixel_id');
    }

    /**
     * Get configured CAPI Access Token
     */
    public function getAccessToken(): ?string
    {
        return Setting::get('meta_capi_token');
    }

    /**
     * Get optional Test Event Code
     */
    public function getTestEventCode(): ?string
    {
        $code = Setting::get('meta_test_event_code');
        return !empty($code) ? trim($code) : null;
    }

    /**
     * Send a single conversion event to Meta Conversions API
     *
     * @param string $eventName Standard event name (PageView, ViewContent, AddToCart, InitiateCheckout, Purchase)
     * @param string $eventId Unique ID for browser-server deduplication
     * @param array $userData User identity information (ip, user_agent, phone, email, etc.)
     * @param array $customData Event-specific custom data (value, currency, contents, content_type, etc.)
     * @param string|null $eventSourceUrl Full URL where the event occurred
     * @return array Response status & details
     */
    public function sendEvent(
        string $eventName,
        string $eventId,
        array $userData = [],
        array $customData = [],
        ?string $eventSourceUrl = null
    ): array {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Meta CAPI is disabled or credentials are missing'
            ];
        }

        $pixelId = $this->getPixelId();
        $token = $this->getAccessToken();
        $url = "{$this->baseUrl}/{$this->graphApiVersion}/{$pixelId}/events?access_token={$token}";

        $formattedUserData = $this->formatUserData($userData);

        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_id' => $eventId,
            'event_source_url' => $eventSourceUrl ?: request()->fullUrl(),
            'action_source' => 'website',
            'user_data' => $formattedUserData,
        ];

        if (!empty($customData)) {
            $eventPayload['custom_data'] = $customData;
        }

        $payload = [
            'data' => [$eventPayload]
        ];

        $testCode = $this->getTestEventCode();
        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->post($url, $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['events_received']) && $data['events_received'] > 0) {
                return [
                    'success' => true,
                    'events_received' => $data['events_received'],
                    'fbtrace_id' => $data['fbtrace_id'] ?? null,
                    'message' => "Meta CAPI event [{$eventName}] sent successfully."
                ];
            }

            Log::warning('Meta CAPI API Error', [
                'event' => $eventName,
                'response' => $data,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => $data['error']['message'] ?? 'Meta Graph API returned an error',
                'error' => $data['error'] ?? null
            ];
        } catch (\Throwable $e) {
            Log::error('Meta CAPI Exception: ' . $e->getMessage(), [
                'event' => $eventName,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Meta CAPI request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send Purchase Event for an Order directly from Server
     */
    public function sendPurchaseEvent(Order $order, string $eventId): array
    {
        $userData = [
            'phone' => $order->customer_phone,
            'name' => $order->customer_name,
            'client_ip_address' => $order->ip_address ?: request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'city' => $order->delivery_zone === 'inside_dhaka' ? 'Dhaka' : '',
            'country' => 'bd',
        ];

        if ($order->user && !empty($order->user->email)) {
            $userData['email'] = $order->user->email;
        }

        $contents = [];
        foreach ($order->items as $item) {
            $contents[] = [
                'id' => (string) $item->product_id,
                'quantity' => (int) $item->quantity,
                'item_price' => (float) $item->unit_price,
            ];
        }

        $customData = [
            'currency' => 'BDT',
            'value' => (float) $order->grand_total,
            'content_type' => 'product',
            'contents' => $contents,
            'num_items' => count($contents),
            'order_id' => $order->order_number,
        ];

        $sourceUrl = route('order.success', $order->order_number);

        return $this->sendEvent('Purchase', $eventId, $userData, $customData, $sourceUrl);
    }

    /**
     * Test connection to Meta Graph API using configured credentials
     */
    public function testConnection(?string $customPixelId = null, ?string $customToken = null, ?string $customTestCode = null): array
    {
        $pixelId = !empty($customPixelId) ? trim($customPixelId) : $this->getPixelId();
        $token = !empty($customToken) ? trim($customToken) : $this->getAccessToken();
        $testCode = !empty($customTestCode) ? trim($customTestCode) : $this->getTestEventCode();

        if (empty($pixelId) || empty($token)) {
            return [
                'success' => false,
                'message' => 'Pixel ID এবং Conversions API Access Token উভয় ফিল্ডই পূরণ করা আবশ্যক।'
            ];
        }

        $url = "{$this->baseUrl}/{$this->graphApiVersion}/{$pixelId}/events?access_token={$token}";

        $testUserData = [];
        $testIp = $this->resolveClientIp();
        if (!empty($testIp)) {
            $testUserData['client_ip_address'] = $testIp;
            $testUserData['client_user_agent'] = request()->userAgent() ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        }

        $testPayload = [
            'data' => [
                [
                    'event_name' => 'PageView',
                    'event_time' => time(),
                    'event_id' => 'test_' . uniqid(),
                    'event_source_url' => url('/'),
                    'action_source' => 'website',
                    'user_data' => $testUserData,
                ]
            ]
        ];

        if (!empty($testCode)) {
            $testPayload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->post($url, $testPayload);

            $data = $response->json();

            if ($response->successful() && isset($data['events_received']) && $data['events_received'] > 0) {
                $msg = "Meta Conversions API সফলভাবে সংযুক্ত হয়েছে! (Events Received: {$data['events_received']})";
                if (!empty($testCode)) {
                    $msg .= " আপনার Events Manager > Test Events ট্যাবে টেস্ট ইভেন্টটি যাচাই করতে পারেন।";
                }
                return [
                    'success' => true,
                    'message' => $msg,
                    'fbtrace_id' => $data['fbtrace_id'] ?? null
                ];
            }

            $errMsg = $data['error']['message'] ?? 'মেটা সার্ভার থেকে ত্রুটি এসেছে। ক্রেডেনশিয়াল পরীক্ষা করুন।';
            return [
                'success' => false,
                'message' => "Meta এপিআই ত্রুটি: {$errMsg}"
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Meta সার্ভারের সাথে সংযোগ করা যায়নি: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Resolve a valid, public client IP address from candidate or request headers (supports Cloudflare, Proxies, cPanel)
     */
    public function resolveClientIp(?string $candidate = null): ?string
    {
        $ip = $candidate;

        if (empty($ip)) {
            $headers = [
                'HTTP_CF_CONNECTING_IP',
                'HTTP_X_REAL_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP',
            ];

            foreach ($headers as $header) {
                $val = request()->server($header);
                if (!empty($val)) {
                    $parts = explode(',', $val);
                    foreach ($parts as $part) {
                        $trimmed = trim($part);
                        if ($this->isValidPublicIp($trimmed)) {
                            return $trimmed;
                        }
                    }
                }
            }

            $ip = request()->ip();
        }

        if (!empty($ip) && $this->isValidPublicIp($ip)) {
            return $ip;
        }

        return null;
    }

    /**
     * Check if an IP address is a valid public, non-reserved, non-loopback IP
     */
    public function isValidPublicIp(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1' || strtolower($ip) === 'localhost') {
            return false;
        }

        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Format and securely hash user data according to Meta Graph API specifications
     */
    protected function formatUserData(array $raw): array
    {
        $userData = [];

        // 1. IP Address (Plaintext) - Resolve valid public IP
        $ip = $this->resolveClientIp($raw['client_ip_address'] ?? ($raw['ip'] ?? null));
        if (!empty($ip)) {
            $userData['client_ip_address'] = $ip;
        }

        // 2. User Agent (Plaintext)
        // Meta Strict Requirement: 'client_user_agent' and 'client_ip_address' MUST be sent together.
        // Sending client_user_agent without client_ip_address triggers Meta's Diagnostics error.
        $ua = $raw['client_user_agent'] ?? ($raw['user_agent'] ?? request()->userAgent());
        if (!empty($ua) && !empty($userData['client_ip_address'])) {
            $userData['client_user_agent'] = $ua;
        }

        // 3. Facebook Cookies (_fbp & _fbc from browser)
        $fbp = $raw['fbp'] ?? request()->cookie('_fbp');
        if (!empty($fbp)) {
            $userData['fbp'] = $fbp;
        }

        $fbc = $raw['fbc'] ?? request()->cookie('_fbc');
        if (!empty($fbc)) {
            $userData['fbc'] = $fbc;
        }

        // 4. Phone Number (SHA-256 Hashed)
        $phone = $raw['ph'] ?? ($raw['phone'] ?? null);
        if (!empty($phone)) {
            $cleanPhone = preg_replace('/[^\d]/', '', (string) $phone);
            // Ensure country code 880 for Bangladesh
            if (str_starts_with($cleanPhone, '01')) {
                $cleanPhone = '88' . $cleanPhone;
            } elseif (str_starts_with($cleanPhone, '1') && strlen($cleanPhone) === 10) {
                $cleanPhone = '880' . $cleanPhone;
            }
            $userData['ph'] = [hash('sha256', $cleanPhone)];
        }

        // 5. Email (SHA-256 Hashed)
        $email = $raw['em'] ?? ($raw['email'] ?? null);
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $cleanEmail = strtolower(trim($email));
            $userData['em'] = [hash('sha256', $cleanEmail)];
        }

        // 6. Name (SHA-256 Hashed)
        $name = $raw['name'] ?? null;
        if (!empty($name)) {
            $parts = explode(' ', trim($name), 2);
            $fn = strtolower(trim($parts[0]));
            $userData['fn'] = [hash('sha256', $fn)];
            if (!empty($parts[1])) {
                $ln = strtolower(trim($parts[1]));
                $userData['ln'] = [hash('sha256', $ln)];
            }
        }

        // 7. City / Country (SHA-256 Hashed)
        if (!empty($raw['city'])) {
            $userData['ct'] = [hash('sha256', strtolower(trim($raw['city'])))];
        }

        if (!empty($raw['country'])) {
            $userData['country'] = [hash('sha256', strtolower(trim($raw['country'])))];
        }

        // 8. External ID (Hashed Session / Customer ID for anonymous visitors and matching)
        $extId = $raw['external_id'] ?? ($raw['user_id'] ?? (session()->has('id') ? session()->getId() : (session()->getId() ?: null)));
        if (!empty($extId)) {
            $userData['external_id'] = [hash('sha256', (string) $extId)];
        }

        return $userData;
    }
}
