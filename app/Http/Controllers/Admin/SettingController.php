<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAll();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'site_name',
            'site_slogan',
            'contact_phone',
            'contact_email',
            'whatsapp_number',
            'contact_address',
            'currency_symbol',
            'footer_about',
            'notice_text',
            
            // Delivery Charges
            'delivery_inside_dhaka',
            'delivery_outside_dhaka',
            'free_delivery_threshold',

            // Payment Information (Checkout page)
            'cod_enabled',
            'cod_instructions',
            'bkash_enabled',
            'bkash_number',
            'bkash_type',
            'bkash_instructions',
            'nagad_enabled',
            'nagad_number',
            'nagad_type',
            'nagad_instructions',

            // Analytics & Pixel
            'meta_pixel_id',

            // Theme & Brand Color Customization
            'theme_primary_color',
            'theme_secondary_color',
            'admin_bg_tint',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            } elseif (in_array($field, ['cod_enabled', 'bkash_enabled', 'nagad_enabled'])) {
                // Checkbox unselected
                Setting::set($field, '0');
            }
        }

        // Handle Logo Upload
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', asset('storage/' . $path));
        }

        // Handle Favicon Upload
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('settings', 'public');
            Setting::set('site_favicon', asset('storage/' . $path));
        }

        Cache::forget('app_settings_all');

        return redirect()->back()->with('success', 'ওয়েবসাইটের সকল সেটিংস এবং পেমেন্ট তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }
}
