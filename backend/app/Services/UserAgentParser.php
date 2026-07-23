<?php

namespace App\Services;

class UserAgentParser
{
    public static function parse(?string $ua): array
    {
        if (!$ua) {
            return ['device_type' => 'desktop', 'device_name' => 'Unknown', 'os_name' => 'Unknown', 'browser_name' => 'Unknown', 'browser_version' => ''];
        }

        return [
            'device_type'     => self::deviceType($ua),
            'device_name'     => self::deviceName($ua),
            'os_name'         => self::os($ua),
            'browser_name'    => self::browser($ua),
            'browser_version' => self::browserVersion($ua),
        ];
    }

    private static function deviceType(string $ua): string
    {
        if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) return 'tablet';
        if (preg_match('/mobile|android|iphone|ipod|windows phone|opera mini|iemobile/i', $ua)) return 'mobile';
        return 'desktop';
    }

    private static function deviceName(string $ua): string
    {
        if (preg_match('/iPhone/i', $ua)) return 'iPhone';
        if (preg_match('/iPad/i', $ua)) return 'iPad';
        if (preg_match('/Pixel (\d+\s?(?:XL|Pro|a)?)/i', $ua, $m)) return 'Google Pixel ' . trim($m[1]);
        if (preg_match('/SM-([A-Z0-9]+)/i', $ua, $m)) return 'Samsung ' . $m[1];
        if (preg_match('/Redmi ([A-Za-z0-9 ]+?)(?:\s+Build|\))/i', $ua, $m)) return 'Redmi ' . trim($m[1]);
        if (preg_match('/ONEPLUS ([A-Z0-9]+)/i', $ua, $m)) return 'OnePlus ' . $m[1];
        if (preg_match('/Windows Phone/i', $ua)) return 'Windows Phone';
        if (preg_match('/Macintosh/i', $ua)) return 'Mac';
        if (preg_match('/Windows/i', $ua)) return 'Windows PC';
        if (preg_match('/Android/i', $ua)) return 'Android Device';
        if (preg_match('/Linux/i', $ua)) return 'Linux PC';
        return 'Unknown Device';
    }

    private static function os(string $ua): string
    {
        if (preg_match('/Windows NT 10\.0/i', $ua)) return 'Windows 10/11';
        if (preg_match('/Windows NT 6\.3/i', $ua)) return 'Windows 8.1';
        if (preg_match('/Windows NT 6\.2/i', $ua)) return 'Windows 8';
        if (preg_match('/Windows NT 6\.1/i', $ua)) return 'Windows 7';
        if (preg_match('/Windows NT 6\.0/i', $ua)) return 'Windows Vista';
        if (preg_match('/Windows NT 5\.1/i', $ua)) return 'Windows XP';
        if (preg_match('/Windows/i', $ua)) return 'Windows';
        if (preg_match('/iPhone OS ([\d_]+)/i', $ua, $m)) return 'iOS ' . str_replace('_', '.', $m[1]);
        if (preg_match('/iPad.*OS ([\d_]+)/i', $ua, $m)) return 'iPadOS ' . str_replace('_', '.', $m[1]);
        if (preg_match('/Mac OS X ([\d_]+)/i', $ua, $m)) return 'macOS ' . str_replace('_', '.', $m[1]);
        if (preg_match('/Android ([\d.]+)/i', $ua, $m)) return 'Android ' . $m[1];
        if (preg_match('/Ubuntu/i', $ua)) return 'Ubuntu';
        if (preg_match('/Linux/i', $ua)) return 'Linux';
        return 'Unknown OS';
    }

    private static function browser(string $ua): string
    {
        if (preg_match('/Edg\//i', $ua)) return 'Microsoft Edge';
        if (preg_match('/OPR|Opera/i', $ua)) return 'Opera';
        if (preg_match('/YaBrowser/i', $ua)) return 'Yandex';
        if (preg_match('/SamsungBrowser/i', $ua)) return 'Samsung Browser';
        if (preg_match('/Chrome/i', $ua)) return 'Chrome';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/Safari/i', $ua)) return 'Safari';
        if (preg_match('/MSIE|Trident/i', $ua)) return 'Internet Explorer';
        return 'Unknown';
    }

    private static function browserVersion(string $ua): string
    {
        $patterns = [
            'Microsoft Edge'   => '/Edg\/([\d.]+)/i',
            'Opera'            => '/(?:OPR|Opera)[\/\s]([\d.]+)/i',
            'Samsung Browser'  => '/SamsungBrowser\/([\d.]+)/i',
            'Chrome'           => '/Chrome\/([\d.]+)/i',
            'Firefox'          => '/Firefox\/([\d.]+)/i',
            'Safari'           => '/Version\/([\d.]+)/i',
            'Internet Explorer'=> '/(?:MSIE |rv:)([\d.]+)/i',
        ];

        $browser = self::browser($ua);
        if (isset($patterns[$browser]) && preg_match($patterns[$browser], $ua, $m)) {
            // Return major.minor only
            $parts = explode('.', $m[1]);
            return implode('.', array_slice($parts, 0, 2));
        }
        return '';
    }
}
