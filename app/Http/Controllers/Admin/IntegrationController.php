<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IntegrationController extends Controller
{
    /**
     * Show the Integrations and Tracking Scripts Management Console
     */
    public function index()
    {
        $settings = [
            // Meta (Facebook)
            'meta_pixel_id' => Setting::get('meta_pixel_id', ''),
            'meta_pixel_enabled' => Setting::get('meta_pixel_enabled', '0'),
            'meta_capi_token' => Setting::get('meta_capi_token', ''),
            'meta_test_event_code' => Setting::get('meta_test_event_code', ''),

            // Google Tag Manager & GA4
            'gtm_container_id' => Setting::get('gtm_container_id', ''),
            'gtm_enabled' => Setting::get('gtm_enabled', '0'),
            'ga4_measurement_id' => Setting::get('ga4_measurement_id', ''),
            'ga4_enabled' => Setting::get('ga4_enabled', '0'),

            // TikTok
            'tiktok_pixel_id' => Setting::get('tiktok_pixel_id', ''),
            'tiktok_pixel_enabled' => Setting::get('tiktok_pixel_enabled', '0'),

            // WhatsApp Chat Widget
            'whatsapp_chat_enabled' => Setting::get('whatsapp_chat_enabled', '0'),
            'whatsapp_number' => Setting::get('whatsapp_number', ''),
            'whatsapp_default_message' => Setting::get('whatsapp_default_message', 'Hello SIDQ MART! I need assistance with an order.'),
            'whatsapp_widget_position' => Setting::get('whatsapp_widget_position', 'bottom-right'),

            // Custom Injected Scripts
            'custom_header_scripts' => Setting::get('custom_header_scripts', ''),
            'custom_footer_scripts' => Setting::get('custom_footer_scripts', ''),

            // Google Sheets Automation
            'google_sheet_enabled' => Setting::get('google_sheet_enabled', '1'),
            'google_sheet_id' => Setting::get('google_sheet_id', '1tQzlrMx-MBDGqrgdeX2PZq-US3e9SRrJwLCoz9Qda_g'),
            'google_sheet_tab_name' => Setting::get('google_sheet_tab_name', 'Orders'),
            'google_sheet_service_account_json' => Setting::get('google_sheet_service_account_json', '') ?: (file_exists(storage_path('app/google/service-account.json')) ? file_get_contents(storage_path('app/google/service-account.json')) : ''),
        ];

        return view('admin.integrations.index', compact('settings'));
    }

    /**
     * Save/Update Integration Settings
     */
    public function update(Request $request)
    {
        $fields = [
            'meta_pixel_id',
            'meta_pixel_enabled',
            'meta_capi_token',
            'meta_test_event_code',
            'gtm_container_id',
            'gtm_enabled',
            'ga4_measurement_id',
            'ga4_enabled',
            'tiktok_pixel_id',
            'tiktok_pixel_enabled',
            'whatsapp_chat_enabled',
            'whatsapp_number',
            'whatsapp_default_message',
            'whatsapp_widget_position',
            'custom_header_scripts',
            'custom_footer_scripts',
            'google_sheet_enabled',
            'google_sheet_id',
            'google_sheet_tab_name',
            'google_sheet_service_account_json',
        ];

        // Process checkboxes that might not be in request if unchecked
        $toggleKeys = [
            'meta_pixel_enabled',
            'gtm_enabled',
            'ga4_enabled',
            'tiktok_pixel_enabled',
            'whatsapp_chat_enabled',
            'google_sheet_enabled',
        ];

        foreach ($toggleKeys as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0');
        }

        foreach ($fields as $field) {
            if (!in_array($field, $toggleKeys)) {
                Setting::set($field, $request->input($field, ''));
            }
        }

        Cache::forget('app_settings_all');

        return redirect()->route('admin.integrations.index')->with('success', 'Marketing integrations and tracking scripts updated successfully!');
    }

    /**
     * Test Google Sheet Connection via AJAX
     */
    public function testGoogleSheet(Request $request, \App\Services\GoogleSheetService $sheetService)
    {
        $sheetId = $request->input('google_sheet_id');
        $tab = $request->input('google_sheet_tab_name');
        $customJson = $request->input('google_sheet_service_account_json');

        $result = $sheetService->testConnection($sheetId, $tab, $customJson);

        return response()->json($result);
    }
}
