<?php
/**
 * Zira project.
 * cookie.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

class Cookie {
    const DEFAULT_LIFETIME = 31536000; // one year
    const DEFAULT_PATH = '/';
    const DEFAULT_HTTP_ONLY = true;

    public static function get($var) {
        if (!isset($_COOKIE[$var])) return null;
        return $_COOKIE[$var];
    }

    public static function set($var, $val, $lifetime = null, $path = null, $domain = null, $secure = null, $http_only = null) {
        if ($lifetime === null) {
            $lifetime = self::DEFAULT_LIFETIME;
            $expire = time()+$lifetime;
        } else if (!empty($lifetime)) {
            $expire = time()+$lifetime;
        } else {
            $expire = null;
        }
        if ($path === null) $path = self::DEFAULT_PATH;
        if ($http_only === null) $http_only = self::DEFAULT_HTTP_ONLY;
        setcookie($var, $val, $expire, $path, $domain, $secure, $http_only);
    }

    public static function remove($var, $path = null, $domain = null, $secure = null, $http_only = null) {
        if ($path === null) $path = self::DEFAULT_PATH;
        if ($http_only === null) $http_only = self::DEFAULT_HTTP_ONLY;
        setcookie($var, null, time()-1, $path, $domain, $secure, $http_only);
    }
}