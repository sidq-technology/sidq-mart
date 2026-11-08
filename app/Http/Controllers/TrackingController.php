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

        if ($fbp = $request->cookie('_fbp')) {
            $userData['fbp'] = $fbp;
        }
        if ($fbc = $request->cookie('_fbc')) {
            $userData['fbc'] = $fbc;
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
