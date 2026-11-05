<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpLocationService
{
    protected string $apiUrl = 'http://ip-api.com/json';

    /**
     * Lookup IP location details with caching and private IP handling
     */
    public function lookup(string $ip): array
    {
        $ip = trim($ip);

        // Check if IP is a local / private / loopback address
        if ($this->isLocalOrPrivateIp($ip)) {
            return [
                'success' => true,
                'is_local' => true,
                'ip' => $ip,
                'location' => 'লোকালহোস্ট (Development Server)',
                'city' => 'Localhost',
                'region' => 'Development',
                'district' => '',
                'country' => 'Local Network',
                'country_code' => 'LOCAL',
                'isp' => 'Local Loopback Network',
                'is_mobile' => false,
                'is_proxy' => false,
                'map_url' => null,
                'message' => 'এটি আপনার লোকাল কম্পিউটারের আইপি (127.0.0.1)। লাইভ সার্ভারে কাস্টমারের আসল ভৌগোলিক অবস্থান ও ISP তথ্য প্রদর্শিত হবে।'
            ];
        }

        // Cache lookup results for 7 days to conserve rate limit and provide instantaneous response
        $cacheKey = 'ip_geo_' . md5($ip);

        return Cache::remember($cacheKey, 86400 * 7, function () use ($ip) {
            try {
                $response = Http::timeout(5)->get("{$this->apiUrl}/{$ip}", [
                    'fields' => 'status,message,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,isp,org,as,mobile,proxy,hosting,query'
                ]);

                if (!$response->successful()) {
                    return [
                        'success' => false,
                        'is_local' => false,
                        'ip' => $ip,
                        'message' => 'আইপি সার্ভারের সাথে যোগাযোগ করা যায়নি (HTTP ' . $response->status() . ')'
                    ];
                }

                $data = $response->json();

                if (($data['status'] ?? '') !== 'success') {
                    return [
                        'success' => false,
                        'is_local' => false,
                        'ip' => $ip,
                        'message' => $data['message'] ?? 'আইপি লোকেশন পাওয়া যায়নি'
                    ];
                }

                $city = $data['city'] ?? '';
                $region = $data['regionName'] ?? '';
                $district = $data['district'] ?? '';
                $country = $data['country'] ?? 'Bangladesh';
                $countryCode = $data['countryCode'] ?? 'BD';

                // Format friendly location label: e.g. "Dhaka, Dhaka Division, BD"
                $locParts = array_filter([$city, $district, $region, $countryCode]);
                $locationText = implode(', ', array_unique($locParts));

                $isProxy = (bool) (($data['proxy'] ?? false) || ($data['hosting'] ?? false));
                $isMobile = (bool) ($data['mobile'] ?? false);

                $mapUrl = null;
                if (!empty($data['lat']) && !empty($data['lon'])) {
                    $mapUrl = "https://www.google.com/maps?q={$data['lat']},{$data['lon']}";
                }

                return [
                    'success' => true,
                    'is_local' => false,
                    'ip' => $ip,
                    'location' => $locationText ?: $country,
                    'city' => $city,
                    'region' => $region,
                    'district' => $district,
                    'country' => $country,
                    'country_code' => $countryCode,
                    'zip' => $data['zip'] ?? '',
                    'isp' => $data['isp'] ?? ($data['org'] ?? 'Unknown ISP'),
                    'is_mobile' => $isMobile,
                    'is_proxy' => $isProxy,
                    'lat' => $data['lat'] ?? null,
                    'lon' => $data['lon'] ?? null,
                    'map_url' => $mapUrl,
                    'message' => 'সফলভাবে আইপি তথ্য লোড হয়েছে'
                ];
            } catch (\Throwable $e) {
                Log::warning("IP Lookup failed for {$ip}: " . $e->getMessage());
                return [
                    'success' => false,
                    'is_local' => false,
                    'ip' => $ip,
                    'message' => 'আইপি ডেটা ফেচ ত্রুটি: ' . $e->getMessage()
                ];
            }
        });
    }

    /**
     * Check if given IP is private / loopback / local
     */
    protected function isLocalOrPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost', '0.0.0.0'])) {
            return true;
        }

        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
