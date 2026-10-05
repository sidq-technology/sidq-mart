<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Check if master SMS switch is active.
     */
    public function isSmsEnabled(): bool
    {
        return Setting::get('sms_enabled', '0') === '1';
    }

    /**
     * Check if a specific order event notification is enabled.
     */
    public function isEventEnabled(string $event): bool
    {
        if (!$this->isSmsEnabled()) {
            return false;
        }

        return Setting::get("sms_event_{$event}", '1') === '1';
    }

    /**
     * Format and normalize Bangladeshi phone numbers to 8801XXXXXXXXX standard.
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $bengaliDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $englishDigits = ['0','1','2','3','4','5','6','7','8','9'];
        $clean = str_replace($bengaliDigits, $englishDigits, $phone);
        $clean = preg_replace('/[^\d]/', '', $clean);

        if (str_starts_with($clean, '8801') && strlen($clean) === 13) {
            return $clean;
        }

        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            return '88' . $clean;
        }

        if (str_starts_with($clean, '1') && strlen($clean) === 10) {
            return '880' . $clean;
        }

        return $clean;
    }

    /**
     * Queue an SMS for non-blocking asynchronous transmission.
     */
    public function send(string $phoneNumber, string $message, string $purpose = 'transactional', ?int $orderId = null): ?SmsLog
    {
        $recipient = self::formatPhoneNumber($phoneNumber);

        if (empty($recipient) || empty(trim($message))) {
            Log::warning("SmsService: Skipped sending SMS due to empty recipient or message.");
            return null;
        }

        // Create log record initially marked as queued
        $log = SmsLog::create([
            'recipient' => $recipient,
            'message' => $message,
            'purpose' => $purpose,
            'order_id' => $orderId,
            'status' => 'queued',
            'provider' => Setting::get('sms_provider', 'mram'),
            'response_data' => null,
        ]);

        // Dispatch background job after sending response to client (instant checkout/admin UX)
        SendSmsJob::dispatch($recipient, $message, $purpose, $orderId, $log->id)->afterResponse();

        return $log;
    }

    /**
     * Execute the physical HTTP API call to MRAM Technologies Ltd (or custom gateway).
     */
    public function executeSend(string $recipient, string $message, string $purpose = 'transactional', ?int $orderId = null, ?int $logId = null): array
    {
        $apiKey = Setting::get('sms_api_key', '');
        $senderId = Setting::get('sms_sender_id', '');
        $apiUrl = Setting::get('sms_api_url', 'https://sms.mram.com.bd/smsapi');
        $type = Setting::get('sms_type', 'unicode');
        $label = Setting::get('sms_label', 'transactional');

        if (empty($apiKey) || empty($senderId)) {
            $errorMsg = 'SMS credentials missing: API Key or Sender ID is not configured in Admin Settings.';
            Log::warning("SmsService::executeSend - {$errorMsg}");

            if ($logId) {
                SmsLog::where('id', $logId)->update([
                    'status' => 'failed',
                    'response_data' => $errorMsg,
                ]);
            }

            return ['success' => false, 'message' => $errorMsg];
        }

        $postData = [
            'api_key' => $apiKey,
            'type' => $type,
            'contacts' => $recipient,
            'senderid' => $senderId,
            'msg' => $message,
            'label' => $label,
        ];

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->withoutVerifying()
                ->post($apiUrl, $postData);

            $body = $response->body();
            $statusCode = $response->status();

            // Check if MRAM returned successful confirmation or error code
            // Error codes: 1002, 1003, 1004, 1007 (insufficient balance), 1012, etc.
            $isSuccess = $response->successful() && !preg_match('/^(100[1-9]|101[0-9])\b/', trim($body));

            // MRAM error code mapping for clear admin feedback
            $errorMap = [
                '1002' => 'অনুমোদিত সেন্ডার আইডি পাওয়া যায়নি (1002: Sender Id/Masking Not Found)',
                '1003' => 'API কী পাওয়া যায়নি বা নিষ্ক্রিয় (1003: API Not Found)',
                '1004' => 'স্প্যাম শনাক্ত হয়েছে (1004: SPAM Detected)',
                '1005' => 'MRAM অভ্যন্তরীণ সার্ভার ত্রুটি (1005: Internal Error)',
                '1006' => 'MRAM অভ্যন্তরীণ সার্ভার ত্রুটি (1006: Internal Error)',
                '1007' => 'পর্যাপ্ত SMS ব্যালেন্স নেই (1007: Balance Insufficient)',
                '1008' => 'মেসেজ বডি ফাঁকা (1008: Message is empty)',
                '1009' => 'কন্টেন্ট টাইপ নির্ধারণ করা হয়নি (1009: Message Type Not Set)',
                '1012' => 'অবৈধ মোবাইল নম্বর (1012: Invalid Number)',
                '1013' => 'API লিমিট অতিক্রম করেছে (1013: API Limit Error)',
                '1014' => 'টেমপ্লেট মেলেনি (1014: No Matching Template)',
                '1015' => 'SMS কন্টেন্ট ভ্যালিডেশন ব্যর্থ (1015: Content Validation Fails)',
                '1016' => 'আইপি হোয়াইটলিস্ট করা নেই (1016: IP Address Not Allowed)',
                '1019' => 'SMS উদ্দেশ্য বা লেবেল পাওয়া যায়নি (1019: Sms Purpose Missing)',
            ];

            $cleanBody = trim($body);
            if (isset($errorMap[$cleanBody])) {
                $isSuccess = false;
                $friendlyError = $errorMap[$cleanBody];
            } else {
                $friendlyError = $cleanBody;
            }

            // Also check for positive keywords common in MRAM responses like "SUBMITTED", "SMS SUBMITTED", "success"
            if (stripos($body, 'SUBMITTED') !== false || stripos($body, 'success') !== false) {
                $isSuccess = true;
            } elseif (is_numeric($cleanBody) && (int)$cleanBody >= 1000) {
                $isSuccess = false;
            }

            $statusText = $isSuccess ? 'sent' : 'failed';

            if ($logId) {
                SmsLog::where('id', $logId)->update([
                    'status' => $statusText,
                    'response_data' => $friendlyError,
                ]);
            }

            return [
                'success' => $isSuccess,
                'status_code' => $statusCode,
                'raw' => $friendlyError,
                'message' => $isSuccess ? 'SMS প্রেরিত হয়েছে' : $friendlyError,
            ];
        } catch (\Throwable $e) {
            Log::error("SmsService::executeSend Exception: " . $e->getMessage());

            if ($logId) {
                SmsLog::where('id', $logId)->update([
                    'status' => 'failed',
                    'response_data' => 'Network/cURL Exception: ' . $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve live account balance from MRAM gateway.
     */
    public function getBalance(): ?string
    {
        $apiKey = Setting::get('sms_api_key', '');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $url = "https://sms.mram.com.bd/miscapi/{$apiKey}/getBalance";
            $response = Http::timeout(10)->withoutVerifying()->get($url);

            if ($response->successful()) {
                $body = trim($response->body());
                if (stripos($body, 'Error:') !== false) {
                    return null;
                }
                $json = json_decode($body, true);
                if (is_array($json) && isset($json['balance'])) {
                    return '৳ ' . number_format((float)$json['balance'], 2);
                }
                if (preg_match('/BDT\s*([0-9\.,]+)/i', $body, $matches) || preg_match('/([0-9\.,]+)/', $body, $matches)) {
                    return '৳ ' . $matches[1];
                }
                return $body;
            }
        } catch (\Throwable $e) {
            Log::warning("SmsService::getBalance Exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Dispatches order lifecycle notification if configured and enabled.
     */
    public function sendOrderNotification(Order $order, string $event): bool
    {
        if (!$this->isEventEnabled($event)) {
            return false;
        }

        if (empty($order->customer_phone)) {
            return false;
        }

        $template = $this->getTemplateForEvent($event);
        if (empty($template)) {
            return false;
        }

        $message = $this->parseTemplate($template, $order);

        $this->send($order->customer_phone, $message, "order_{$event}", $order->id);

        return true;
    }

    /**
     * Get template string for specified event from settings or fallback defaults.
     */
    public function getTemplateForEvent(string $event): string
    {
        $defaultTemplates = [
            'order_placed' => 'প্রিয় {customer_name}, {site_name}-এ আপনার অর্ডার #{order_number} সফলভাবে গৃহীত হয়েছে। মোট বিল ৳{grand_total}। ধন্যবাদ!',
            'order_processing' => 'প্রিয় {customer_name}, আপনার অর্ডার #{order_number} প্রসেসিং চলছে। দ্রুতই ডেলিভারি করা হবে। - {site_name}',
            'order_shipped' => 'প্রিয় {customer_name}, আপনার অর্ডার #{order_number} কুরিয়ারে হস্তান্তর করা হয়েছে। খুব শীঘ্রই ডেলিভারি পাবেন। - {site_name}',
            'order_delivered' => 'প্রিয় {customer_name}, আপনার অর্ডার #{order_number} সফলভাবে ডেলিভার্ড হয়েছে। আমাদের সাথে থাকার জন্য ধন্যবাদ! - {site_name}',
            'order_cancelled' => 'প্রিয় {customer_name}, দুঃখিত, আপনার অর্ডার #{order_number} বাতিল করা হয়েছে। প্রয়োজনে যোগাযোগ করুন: {contact_phone}। - {site_name}',
        ];

        return Setting::get("sms_template_{$event}", $defaultTemplates[$event] ?? '');
    }

    /**
     * Parse template placeholders with dynamic order parameters.
     */
    public function parseTemplate(string $template, Order $order): string
    {
        $replacements = [
            '{customer_name}' => $order->customer_name ?? 'গ্রাহক',
            '{order_number}' => $order->order_number ?? '',
            '{grand_total}' => number_format($order->grand_total ?? 0, 0),
            '{site_name}' => Setting::get('site_name', 'SIDQ MART'),
            '{contact_phone}' => Setting::get('contact_phone', ''),
            '{delivery_zone}' => $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
