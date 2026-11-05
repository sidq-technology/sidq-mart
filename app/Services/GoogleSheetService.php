<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected const DEFAULT_SHEET_ID = '1tQzlrMx-MBDGqrgdeX2PZq-US3e9SRrJwLCoz9Qda_g';
    protected const DEFAULT_TAB_NAME = 'Orders';

    /**
     * Get the configured Google Sheet ID
     */
    public function getSheetId(): string
    {
        $id = Setting::get('google_sheet_id') ?: env('GOOGLE_SHEET_ID');
        return !empty($id) ? trim($id) : self::DEFAULT_SHEET_ID;
    }

    /**
     * Get the configured Sheet Tab Name
     */
    public function getTabName(): string
    {
        $tab = Setting::get('google_sheet_tab_name') ?: env('GOOGLE_SHEET_TAB_NAME');
        return !empty($tab) ? trim($tab) : self::DEFAULT_TAB_NAME;
    }

    /**
     * Get the parsed Service Account Credentials
     */
    public function getCredentials(?string $customJson = null): ?array
    {
        $rawJson = $customJson ?: Setting::get('google_sheet_service_account_json');

        if (!empty($rawJson)) {
            $parsed = is_array($rawJson) ? $rawJson : json_decode($rawJson, true);
            if (is_array($parsed) && !empty($parsed['client_email']) && !empty($parsed['private_key'])) {
                return $parsed;
            }
        }

        // Fallback to local storage file
        $filePath = storage_path('app/google/service-account.json');
        if (file_exists($filePath)) {
            $parsed = json_decode(file_get_contents($filePath), true);
            if (is_array($parsed) && !empty($parsed['client_email']) && !empty($parsed['private_key'])) {
                return $parsed;
            }
        }

        return null;
    }

    /**
     * Get Google OAuth2 Access Token using RS256 JWT
     */
    public function getAccessToken(?array $credentials = null, bool $forceRefresh = false): ?string
    {
        $creds = $credentials ?: $this->getCredentials();
        if (!$creds) {
            throw new \Exception('Google Service Account credentials not found or invalid.');
        }

        $cacheKey = 'google_sheets_access_token_' . md5($creds['client_email']);

        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $claims = [
            'iss' => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];
        $payload = $this->base64UrlEncode(json_encode($claims));
        $toSign = $header . '.' . $payload;

        $privateKey = $creds['private_key'];
        $signature = '';
        if (!openssl_sign($toSign, $signature, $privateKey, 'SHA256')) {
            throw new \Exception('Failed to sign Google JWT with private key: ' . openssl_error_string());
        }

        $jwt = $toSign . '.' . $this->base64UrlEncode($signature);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception('Network connection to Google OAuth failed: ' . $curlError);
        }

        $tokenData = json_decode($response, true);
        if ($httpCode !== 200 || empty($tokenData['access_token'])) {
            $msg = $tokenData['error_description'] ?? ($tokenData['error'] ?? 'HTTP ' . $httpCode . ': ' . $response);
            throw new \Exception('Google OAuth Error: ' . $msg);
        }

        $token = $tokenData['access_token'];
        $expiresIn = max(60, ($tokenData['expires_in'] ?? 3600) - 300);
        Cache::put($cacheKey, $token, $expiresIn);

        return $token;
    }

    /**
     * Test connection to Google Spreadsheet
     */
    public function testConnection(?string $sheetId = null, ?string $tab = null, ?string $customJson = null): array
    {
        try {
            $sheetId = !empty($sheetId) ? trim($sheetId) : $this->getSheetId();
            $tab = !empty($tab) ? trim($tab) : $this->getTabName();
            $credentials = $this->getCredentials($customJson);

            if (!$credentials) {
                return [
                    'success' => false,
                    'message' => 'সার্ভিস অ্যাকাউন্ট JSON পাওয়া যায়নি বা অবৈধ।'
                ];
            }

            $token = $this->getAccessToken($credentials, true);
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'গুগল অ্যাক্সেস টোকেন তৈরি করা সম্ভব হয়নি।'
                ];
            }

            // Fetch spreadsheet metadata to verify Sheet ID and Tab Name
            $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}";
            $meta = $this->makeGoogleRequest('GET', $url, $token);

            $availableTabs = [];
            $tabFound = false;
            foreach ($meta['sheets'] ?? [] as $s) {
                $title = $s['properties']['title'] ?? '';
                $availableTabs[] = $title;
                if (strcasecmp($title, $tab) === 0) {
                    $tabFound = true;
                }
            }

            if (!$tabFound) {
                return [
                    'success' => false,
                    'message' => "শীট পাওয়া গেছে ('" . ($meta['properties']['title'] ?? '') . "'), কিন্তু '{$tab}' ট্যাব পাওয়া যায়নি। বিদ্যমান ট্যাবগুলো হলো: " . implode(', ', $availableTabs)
                ];
            }

            return [
                'success' => true,
                'message' => "গুগল শীট সফলভাবে সংযুক্ত হয়েছে! ফাইল: '" . ($meta['properties']['title'] ?? '') . "', ট্যাব: '{$tab}'"
            ];
        } catch (\Throwable $e) {
            Log::error('Google Sheet testConnection failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'ত্রুটি: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single order to Google Sheet 'Orders' tab
     * Keeps all items in 1 row, and updates existing row if already sent.
     */
    public function syncOrder(Order $order): array
    {
        try {
            $sheetId = $this->getSheetId();
            $tab = $this->getTabName();
            $token = $this->getAccessToken();

            $order->loadMissing('items.product');

            // 1. Build row values according to columns A-K
            $rowValues = $this->formatOrderRow($order);

            // 2. Check if Order ID already exists in Column A
            $existingRowIndex = $this->findOrderRowIndex($sheetId, $tab, $order->order_number, $token);

            if ($existingRowIndex > 0) {
                // Update existing row
                $range = urlencode("{$tab}!A{$existingRowIndex}:K{$existingRowIndex}");
                $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/{$range}?valueInputOption=USER_ENTERED";
                
                $this->makeGoogleRequest('PUT', $url, $token, [
                    'range' => "{$tab}!A{$existingRowIndex}:K{$existingRowIndex}",
                    'majorDimension' => 'ROWS',
                    'values' => [$rowValues]
                ]);

                return [
                    'success' => true,
                    'action' => 'updated',
                    'row' => $existingRowIndex,
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে গুগল শীটের {$existingRowIndex} নং রো-তে আপডেট করা হয়েছে!"
                ];
            } else {
                // Append new row
                $range = urlencode("{$tab}!A:K");
                $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/{$range}:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS";

                $res = $this->makeGoogleRequest('POST', $url, $token, [
                    'range' => "{$tab}!A:K",
                    'majorDimension' => 'ROWS',
                    'values' => [$rowValues]
                ]);

                $updatedRange = $res['updates']['updatedRange'] ?? '';

                return [
                    'success' => true,
                    'action' => 'appended',
                    'range' => $updatedRange,
                    'message' => "অর্ডার #{$order->order_number} সফলভাবে গুগল শীটে যুক্ত করা হয়েছে!"
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Failed to sync Order #{$order->order_number} to Google Sheet: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'গুগল শীটে পাঠাতে ব্যর্থ হয়েছে: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format Order object into 11 columns [A..K]
     */
    protected function formatOrderRow(Order $order): array
    {
        $items = $order->items;

        // Column A: Order ID
        $colOrderId = '#' . ltrim($order->order_number, '#');

        // Column B: Order Date
        $orderDate = $order->created_at 
            ? $order->created_at->timezone('Asia/Dhaka')->format('d M Y, h:i A') 
            : date('d M Y, h:i A');

        // Column C: Customer Name
        $colCustomerName = (string) $order->customer_name;

        // Column D: Phone Number
        $colPhone = (string) $order->customer_phone;

        // Column E: Delivery Address
        $areaLabel = $order->delivery_area === 'inside_dhaka' ? 'ঢাকার ভিতরে' : ($order->delivery_area === 'outside_dhaka' ? 'ঢাকার বাইরে' : $order->delivery_area);
        $colAddress = trim($order->shipping_address . ($areaLabel ? " ({$areaLabel})" : ''));

        // Column F: Notes (Admin notes or customer notes)
        $colNotes = trim($order->admin_notes ?: ($order->customer_notes ?: ''));

        // Column G: Product Name (combined in 1 row)
        // Column H: Unit Price
        // Column I: Quantity
        if ($items->isEmpty()) {
            $colProductName = 'N/A';
            $colUnitPrice = '৳0';
            $colQuantity = '0';
        } elseif ($items->count() === 1) {
            $item = $items->first();
            $varAttr = !empty($item->variant_attributes) ? " [{$item->variant_attributes}]" : '';
            $sku = ($item->product && $item->product->sku) ? " ({$item->product->sku})" : '';
            $colProductName = $item->product_name . $varAttr . $sku;
            $colUnitPrice = '৳' . number_format($item->unit_price, 0);
            $colQuantity = (string) $item->quantity;
        } else {
            // Multiple products combined in 1 row
            $prodNames = [];
            $unitPrices = [];
            $quantities = [];

            foreach ($items as $item) {
                $varAttr = !empty($item->variant_attributes) ? " [{$item->variant_attributes}]" : '';
                $sku = ($item->product && $item->product->sku) ? " ({$item->product->sku})" : '';
                $prodNames[] = $item->product_name . $varAttr . $sku . " (x{$item->quantity})";
                $unitPrices[] = '৳' . number_format($item->unit_price, 0);
                $quantities[] = $item->quantity;
            }

            $colProductName = implode(' + ', $prodNames);
            $colUnitPrice = implode(', ', $unitPrices);
            $totalQty = array_sum($quantities);
            $colQuantity = "{$totalQty} (" . implode('+', $quantities) . ")";
        }

        // Column J: Total Amount
        $colTotalAmount = '৳' . number_format($order->grand_total, 0);

        // Column K: Order Status
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $colStatus = $statusLabels[strtolower($order->order_status)] ?? ucfirst($order->order_status);

        return [
            $colOrderId,       // Col A
            $orderDate,        // Col B
            $colCustomerName,  // Col C
            $colPhone,         // Col D
            $colAddress,       // Col E
            $colNotes,         // Col F
            $colProductName,   // Col G
            $colUnitPrice,     // Col H
            $colQuantity,      // Col I
            $colTotalAmount,   // Col J
            $colStatus         // Col K
        ];
    }

    /**
     * Search Column A for Order ID to prevent duplicates
     * Returns 1-based row number if found, or 0 if not found.
     */
    protected function findOrderRowIndex(string $sheetId, string $tab, string $orderNumber, string $token): int
    {
        try {
            $range = urlencode("{$tab}!A:A");
            $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/{$range}?majorDimension=ROWS";
            $data = $this->makeGoogleRequest('GET', $url, $token);

            $rows = $data['values'] ?? [];
            $cleanTarget = strtolower(trim(str_replace('#', '', $orderNumber)));

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header row
                $cellVal = isset($row[0]) ? strtolower(trim(str_replace('#', '', $row[0]))) : '';
                if ($cellVal !== '' && $cellVal === $cleanTarget) {
                    return $index + 1; // 1-based index in Google Sheets
                }
            }
        } catch (\Throwable $e) {
            Log::warning('findOrderRowIndex check error: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Helper to perform authenticated HTTP requests to Google Sheets API
     */
    protected function makeGoogleRequest(string $method, string $url, string $token, ?array $payload = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            "Authorization: Bearer {$token}",
            "Accept: application/json"
        ];

        if ($payload !== null) {
            $jsonData = json_encode($payload);
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception('CURL Error contacting Google API: ' . $curlError);
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 400) {
            $msg = $decoded['error']['message'] ?? ($decoded['error_description'] ?? "HTTP {$httpCode}: {$response}");
            throw new \Exception($msg);
        }

        return is_array($decoded) ? $decoded : [];
    }

    protected function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
