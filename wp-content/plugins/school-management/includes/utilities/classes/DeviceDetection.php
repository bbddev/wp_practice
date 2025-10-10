<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Device Detection Class
 * Xử lý nhận diện thiết bị, trình duyệt, IP
 */
class DeviceDetection
{
    /**
     * Lấy địa chỉ IP của user (xử lý cả proxy và load balancer)
     * 
     * @return string IP address
     */
    public static function getUserIP()
    {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        );

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim($_SERVER[$key]);
                // Nếu có nhiều IP (qua proxy), lấy IP đầu tiên
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR nếu không tìm thấy public IP
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    }

    /**
     * Parse User Agent để lấy thông tin trình duyệt và hệ điều hành
     * 
     * @param string $user_agent User agent string
     * @return array Thông tin thiết bị
     */
    public static function parseUserAgent($user_agent)
    {
        if (empty($user_agent)) {
            return array(
                'browser' => 'Unknown',
                'os' => 'Unknown',
                'platform' => 'Unknown'
            );
        }

        return array(
            'browser' => self::detectBrowser($user_agent),
            'os' => self::detectOS($user_agent),
            'platform' => self::detectPlatform($user_agent)
        );
    }

    /**
     * Nhận diện trình duyệt
     * 
     * @param string $user_agent User agent string
     * @return string Browser name
     */
    private static function detectBrowser($user_agent)
    {
        if (preg_match('/Edge\/([0-9\.]+)/', $user_agent)) {
            return 'Microsoft Edge';
        } elseif (preg_match('/Chrome\/([0-9\.]+)/', $user_agent)) {
            return 'Google Chrome';
        } elseif (preg_match('/Firefox\/([0-9\.]+)/', $user_agent)) {
            return 'Mozilla Firefox';
        } elseif (preg_match('/Safari\/([0-9\.]+)/', $user_agent) && !preg_match('/Chrome/', $user_agent)) {
            return 'Safari';
        } elseif (preg_match('/Opera\/([0-9\.]+)/', $user_agent) || preg_match('/OPR\/([0-9\.]+)/', $user_agent)) {
            return 'Opera';
        } elseif (preg_match('/MSIE ([0-9\.]+)/', $user_agent) || preg_match('/Trident\//', $user_agent)) {
            return 'Internet Explorer';
        }

        return 'Unknown';
    }

    /**
     * Nhận diện hệ điều hành
     * 
     * @param string $user_agent User agent string
     * @return string OS name
     */
    private static function detectOS($user_agent)
    {
        if (preg_match('/Windows NT 10/', $user_agent)) {
            return 'Windows 10';
        } elseif (preg_match('/Windows NT 6\.3/', $user_agent)) {
            return 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6\.2/', $user_agent)) {
            return 'Windows 8';
        } elseif (preg_match('/Windows NT 6\.1/', $user_agent)) {
            return 'Windows 7';
        } elseif (preg_match('/Windows NT/', $user_agent)) {
            return 'Windows';
        } elseif (preg_match('/Mac OS X ([0-9_\.]+)/', $user_agent, $matches)) {
            return 'Mac OS X ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/iPhone OS ([0-9_\.]+)/', $user_agent, $matches)) {
            return 'iOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Android ([0-9\.]+)/', $user_agent, $matches)) {
            return 'Android ' . $matches[1];
        } elseif (preg_match('/Linux/', $user_agent)) {
            return 'Linux';
        }

        return 'Unknown';
    }

    /**
     * Nhận diện loại thiết bị
     * 
     * @param string $user_agent User agent string
     * @return string Platform type
     */
    private static function detectPlatform($user_agent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/', $user_agent)) {
            if (preg_match('/iPad/', $user_agent)) {
                return 'Tablet';
            } else {
                return 'Mobile';
            }
        }

        return 'Desktop';
    }
}