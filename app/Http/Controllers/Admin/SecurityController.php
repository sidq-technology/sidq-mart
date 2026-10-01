<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $settings = [
            'order_protection_enabled' => Setting::get('order_protection_enabled', '1'),
            'order_protection_max_orders' => Setting::get('order_protection_max_orders', '2'),
            'order_protection_time_window' => Setting::get('order_protection_time_window', '30'),
            'order_protection_track_by' => Setting::get('order_protection_track_by', 'phone_and_ip'),
            'order_protection_block_message' => Setting::get('order_protection_block_message', 'আপনি সম্প্রতি একটি অর্ডার প্লেস করেছেন। ফেক বা অতিরিক্ত অর্ডার রোধে সাময়িকভাবে পুনরায় অর্ডার গ্রহণ স্থগিত রয়েছে। জরুরি প্রয়োজনে আমাদের হটলাইনে যোগাযোগ করুন।'),
            
            'ip_blocking_enabled' => Setting::get('ip_blocking_enabled', '0'),
            'blocked_ips' => Setting::get('blocked_ips', ''),
            'ip_block_message' => Setting::get('ip_block_message', 'আপনার আইপি অ্যাড্রেস থেকে সাময়িকভাবে অর্ডার বা এক্সেস স্থগিত রাখা হয়েছে। সহযোগিতার জন্য আমাদের সাপোর্ট নম্বরে যোগাযোগ করুন।'),
        ];

        // Parse blocked IPs list
        $rawIps = $settings['blocked_ips'];
        $blockedIpsList = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawIps))));
        $blockedCount = count($blockedIpsList);

        $currentIp = $request->ip();

        return view('admin.security.index', compact(
            'settings',
            'blockedIpsList',
            'blockedCount',
            'currentIp'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'order_protection_enabled' => 'nullable|in:0,1',
            'order_protection_max_orders' => 'required|integer|min:1|max:50',
            'order_protection_time_window' => 'required|integer|min:1|max:1440',
            'order_protection_track_by' => 'required|in:phone,ip,phone_and_ip',
            'order_protection_block_message' => 'required|string|max:500',

            'ip_blocking_enabled' => 'nullable|in:0,1',
            'ip_block_message' => 'required|string|max:500',
        ]);

        Setting::set('order_protection_enabled', $request->has('order_protection_enabled') ? '1' : '0');
        Setting::set('order_protection_max_orders', (string) $validated['order_protection_max_orders']);
        Setting::set('order_protection_time_window', (string) $validated['order_protection_time_window']);
        Setting::set('order_protection_track_by', $validated['order_protection_track_by']);
        Setting::set('order_protection_block_message', $validated['order_protection_block_message']);

        Setting::set('ip_blocking_enabled', $request->has('ip_blocking_enabled') ? '1' : '0');
        Setting::set('ip_block_message', $validated['ip_block_message']);

        if ($request->has('blocked_ips')) {
            Setting::set('blocked_ips', $request->input('blocked_ips') ?? '');
        }

        Cache::forget('app_settings_all');

        return redirect()->back()->with('success', 'সিকিউরিটি কনফিগারেশন সফলভাবে আপডেট করা হয়েছে।');
    }

    public function addIp(Request $request)
    {
        $validated = $request->validate([
            'ip' => ['required', 'string', 'max:50', 'regex:/^([0-9a-fA-F:\.]+)$/'],
        ], [
            'ip.required' => 'অনুগ্রহ করে একটি সঠিক আইপি অ্যাড্রেস দিন।',
            'ip.regex' => 'আইপি ফরম্যাট সঠিক নয় (যেমন: 103.145.22.10 বা IPv6)।',
        ]);

        $newIp = trim($validated['ip']);
        $rawIps = Setting::get('blocked_ips', '');
        $list = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawIps))));

        if (in_array($newIp, $list, true)) {
            return redirect()->back()->with('error', "আইপি '{$newIp}' ইতোমধ্যেই ব্লকলিস্টে রয়েছে।");
        }

        array_unshift($list, $newIp);
        Setting::set('blocked_ips', implode("\n", $list));
        
        // Auto-enable IP blocking if disabled
        Setting::set('ip_blocking_enabled', '1');

        Cache::forget('app_settings_all');

        return redirect()->back()->with('success', "আইপি '{$newIp}' সফলভাবে ব্লকলিস্টে যুক্ত করা হয়েছে।");
    }

    public function removeIp(Request $request)
    {
        $validated = $request->validate([
            'ip' => 'required|string',
        ]);

        $targetIp = trim($validated['ip']);
        $rawIps = Setting::get('blocked_ips', '');
        $list = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawIps))));

        $list = array_values(array_filter($list, fn($ip) => $ip !== $targetIp));
        Setting::set('blocked_ips', implode("\n", $list));

        Cache::forget('app_settings_all');

        return redirect()->back()->with('success', "আইপি '{$targetIp}' ব্লকলিস্ট থেকে আনব্লক/মুছে ফেলা হয়েছে।");
    }
}
