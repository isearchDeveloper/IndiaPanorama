<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocation
{
    private static array $localIps = ['127.0.0.1', '::1', 'localhost'];

    public static function lookup(?string $ip): array
    {
        $blank = ['country' => null, 'state' => null, 'city' => null];

        if (!$ip) return $blank;

        if (in_array($ip, self::$localIps) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['country' => 'Local', 'state' => 'Local', 'city' => 'Local'];
        }

        return Cache::remember("geo_{$ip}", 86400, function () use ($ip, $blank) {
            try {
                $resp = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,regionName,city',
                ]);
                if ($resp->successful()) {
                    $data = $resp->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'state'   => $data['regionName'] ?? null,
                            'city'    => $data['city'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('GeoLocation lookup failed: ' . $e->getMessage());
            }
            return $blank;
        });
    }

    public static function format(array $geo): string
    {
        return implode(', ', array_filter([$geo['city'] ?? null, $geo['state'] ?? null, $geo['country'] ?? null])) ?: '—';
    }
}
