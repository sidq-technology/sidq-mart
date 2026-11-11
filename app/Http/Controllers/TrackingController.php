<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaCapiEvent;
use App\Services\MetaCapiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackingController extends Controller
{
    /**
     * Handle incoming async beacon / fetch from frontend for Meta CAPI
     */
    public function trackCapi(Request $request, MetaCapiService $capiService): Response
    {
        if (!$capiService->isEnabled()) {
            return response()->noContent();
        }

        $validated = $request->validate([
            'event_name' => 'required|string|in:PageView,ViewContent,AddToCart,InitiateCheckout,Purchase,Search',
            'event_id' => 'required|string|max:120',
            'custom_data' => 'nullable|array',
            'user_data' => 'nullable|array',
            'event_source_url' => 'nullable|string|max:500',
        ]);

        $userData = $validated['user_data'] ?? [];
        $userData['client_ip_address'] = $capiService->resolveClientIp($request->ip());
        $userData['client_user_agent'] = $request->userAgent();

        if (empty($userData['external_id']) && $request->hasSession()) {
            $userData['external_id'] = $request->session()->getId();
        }

        if (empty($userData['fbp']) && ($fbp = $request->cookie('_fbp'))) {
            $userData['fbp'] = $fbp;
        }
        if (empty($userData['fbp']) && $request->hasSession() && $request->session()->has('meta_capi_fbp')) {
            $userData['fbp'] = $request->session()->get('meta_capi_fbp');
        }
        if (empty($userData['fbp'])) {
            $userData['fbp'] = 'fb.1.' . (time() * 1000) . '.' . mt_rand(1000000000, 9999999999);
            if ($request->hasSession()) {
                $request->session()->put('meta_capi_fbp', $userData['fbp']);
            }
        }

        if (empty($userData['fbc']) && ($fbc = $request->cookie('_fbc'))) {
            $userData['fbc'] = $fbc;
        }
        if (empty($userData['fbc']) && $request->hasSession() && $request->session()->has('meta_capi_fbc')) {
            $userData['fbc'] = $request->session()->get('meta_capi_fbc');
        }

        // Fallback: Recover fbc if fbclid is in current query or referer
        if (empty($userData['fbc'])) {
            $fbclid = $request->query('fbclid');
            if (empty($fbclid) && $request->headers->has('referer')) {
                $refQuery = parse_url($request->header('referer'), PHP_URL_QUERY);
                if (!empty($refQuery)) {
                    parse_str($refQuery, $refParams);
                    $fbclid = $refParams['fbclid'] ?? null;
                }
            }
            if (!empty($fbclid)) {
                $userData['fbc'] = 'fb.1.' . (time() * 1000) . '.' . $fbclid;
                if ($request->hasSession()) {
                    $request->session()->put('meta_capi_fbc', $userData['fbc']);
                }
            }
        }

        $customData = $validated['custom_data'] ?? [];
        $sourceUrl = $validated['event_source_url'] ?? $request->header('referer') ?: url()->previous();

        // Dispatch asynchronously after sending HTTP 204 response
        dispatch(function () use ($capiService, $validated, $userData, $customData, $sourceUrl) {
            $capiService->sendEvent(
                $validated['event_name'],
                $validated['event_id'],
                $userData,
                $customData,
                $sourceUrl
            );
        })->afterResponse();

        return response()->noContent();
    }
}
