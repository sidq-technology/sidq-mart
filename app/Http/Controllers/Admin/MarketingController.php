<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketingController extends Controller
{
    public function smsIndex(Request $request, SmsService $smsService)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $logs = SmsLog::with('order')
            ->when($search, function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_sent' => SmsLog::where('status', 'sent')->count(),
            'total_queued' => SmsLog::where('status', 'queued')->count(),
            'total_failed' => SmsLog::where('status', 'failed')->count(),
            'today_sent' => SmsLog::where('status', 'sent')->whereDate('created_at', today())->count(),
        ];

        $balance = $smsService->getBalance();
        $settings = Setting::getAll();

        return view('admin.marketing.sms', compact('logs', 'stats', 'balance', 'settings', 'search', 'status'));
    }

    public function sendTestSms(Request $request, SmsService $smsService)
    {
        $request->validate([
            'test_phone' => 'required|string|min:11|max:20',
            'test_message' => 'required|string|max:300',
        ]);

        $recipient = $request->input('test_phone');
        $message = $request->input('test_message');

        $result = $smsService->executeSend($recipient, $message, 'test');

        if (!empty($result['success'])) {
            return redirect()->back()->with('success', "টেস্ট SMS সফলভাবে প্রেরণ করা হয়েছে! API রেসপন্স: " . ($result['raw'] ?? 'Success'));
        }

        $errorMsg = $result['message'] ?? ($result['raw'] ?? 'অজানা ত্রুটি');
        return redirect()->back()->with('error', "টেস্ট SMS প্রেরণ ব্যর্থ হয়েছে। বিস্তারিত: {$errorMsg}");
    }

    public function quickToggle(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value', '0');

        $allowedKeys = [
            'sms_enabled',
            'sms_event_order_placed',
            'sms_event_order_processing',
            'sms_event_order_shipped',
            'sms_event_order_delivered',
            'sms_event_order_cancelled',
        ];

        if (in_array($key, $allowedKeys)) {
            Setting::set($key, $value === '1' ? '1' : '0');
            Cache::forget('app_settings_all');

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'key' => $key, 'value' => $value]);
            }

            return redirect()->back()->with('success', 'SMS স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
        }

        return redirect()->back()->with('error', 'অবৈধ সেটিং প্যারামিটার।');
    }

    public function checkBalance(SmsService $smsService)
    {
        $balance = $smsService->getBalance();

        return response()->json([
            'success' => $balance !== null,
            'balance' => $balance ?: 'N/A',
        ]);
    }
}
