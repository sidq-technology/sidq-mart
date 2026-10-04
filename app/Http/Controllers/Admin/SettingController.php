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
        $allProducts = \App\Models\Product::where('is_active', true)->orderBy('name')->get();
        $selectedUpsellProductIds = json_decode($settings['upsell_product_ids'] ?? '[]', true) ?: [];

        return view('admin.settings.index', compact('settings', 'allProducts', 'selectedUpsellProductIds'));
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
            'copyright_text',
            'order_notification_email',
            
            // Delivery Charges & Times
            'delivery_inside_dhaka',
            'delivery_outside_dhaka',
            'free_delivery_threshold',
            'delivery_time_inside',
            'delivery_time_outside',
            'invoice_footer_note',

            // Social Media Links
            'facebook_url',
            'instagram_url',
            'youtube_url',

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
            'rocket_enabled',
            'rocket_number',
            'rocket_type',
            'rocket_instructions',

            // Analytics & Pixel
            'meta_pixel_id',
            'meta_pixel_enabled',

            // Theme & Brand Color Customization
            'theme_primary_color',
            'theme_secondary_color',
            'admin_bg_tint',

            // Thank You Page 1-Click Upsell
            'upsell_badge_text',
            'upsell_heading',
            'upsell_subtitle',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            } elseif (in_array($field, ['bkash_enabled', 'nagad_enabled', 'rocket_enabled', 'meta_pixel_enabled'])) {
                // Checkbox unselected
                Setting::set($field, '0');
            } elseif ($field === 'cod_enabled') {
                $codVal = $request->input('cod_enabled', '0');
                if ($codVal === '0' && !$request->has('bkash_enabled') && !$request->has('nagad_enabled')) {
                    $codVal = '1'; // Guarantee at least one payment method stays enabled
                }
                Setting::set('cod_enabled', $codVal);
            }
        }

        // Handle Upsell Toggle & Products
        if ($request->has('upsell_settings_submitted')) {
            Setting::set('upsell_enabled', $request->has('upsell_enabled') ? '1' : '0');
            $upsellIds = $request->input('upsell_product_ids', []);
            if (is_array($upsellIds)) {
                $upsellIds = array_values(array_unique(array_filter($upsellIds)));
                $upsellIds = array_slice($upsellIds, 0, 3); // Max 3 products
            } else {
                $upsellIds = [];
            }
            Setting::set('upsell_product_ids', json_encode($upsellIds));
        }

        // Handle Logo Upload
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', 'storage/' . $path);
        }

        // Handle Favicon Upload
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('settings', 'public');
            Setting::set('site_favicon', 'storage/' . $path);
        }

        Cache::forget('app_settings_all');

        return redirect()->back()->with('success', 'ওয়েবসাইটের সকল সেটিংস এবং পেমেন্ট তথ্য সফলভাবে আপডেট করা হয়েছে।');
    }
}
