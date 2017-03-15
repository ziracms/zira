<?php
/**
 * Zira project
 * request.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class Request {
    const POST = 'POST';
    const GET = 'GET';
    const FILES = 'FILES';
    
    protected static $_mobile;

    public static function post($var=null, $default = null) {
        if (!$var) return $_POST;
        if (!isset($_POST[$var])) return $default;
        return $_POST[$var];
    }

    public static function setPost($var, $val) {
        $_POST[$var] = $val;
    }

    public static function get($var=null, $default = null) {
        if (!$var) return $_GET;
        if (!isset($_GET[$var])) return $default;
        return $_GET[$var];
    }

    public static function setGet($var, $val) {
        $_GET[$var] = $val;
    }

    public static function file($var=null, $default = null) {
        if (!$var) return $_FILES;
        if (!isset($_FILES[$var])) return $default;
        return $_FILES[$var];
    }

    public static function setFile($var, $val) {
        $_FILES[$var] = $val;
    }

    public static function uri() {
        if (!isset($_SERVER['REQUEST_URI'])) return '';
        return $_SERVER['REQUEST_URI'];
    }

    public static function ip() {
        if (!isset($_SERVER['REMOTE_ADDR'])) return '';
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function method() {
        if (!isset($_SERVER['REQUEST_METHOD'])) return '';
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost() {
        return self::method() == self::POST;
    }

    public static function isGet() {
        return self::method() == self::GET;
    }

    public static function isAjax() {
        return (self::isJson());
    }
    
    public static function isJson() {
        if (self::get(FORMAT_GET_VAR)==FORMAT_JSON || self::post(FORMAT_POST_VAR)==FORMAT_JSON) return true;
        return false;
    }

    public static function isRedirected() {
        return (bool)Session::get(Response::SESSION_REDIRECT);
    }

    public static function detectBaseUrl() {
        $root = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
        $root_parts = explode(DIRECTORY_SEPARATOR, $root);

        $uri = trim($_SERVER['REQUEST_URI'],'/');
        $uri = preg_replace('/^([^\?]+).*$/', '$1', $uri);
        $uri_parts = explode('/', $uri);
        if ($uri_parts[count($uri_parts)-1] == 'index.php') array_pop($uri_parts);

        $_parts = array();
        $found = false;
        while(count($uri_parts)>0) {
            $part = array_pop($uri_parts);
            if ($found || $part == $root_parts[count($root_parts)-1]) {
                $_parts []= $part;
                $found = true;
            }
            if ($found) {
                array_pop($root_parts);
            }
        }

        $base_url = '/' . implode('/', array_reverse($_parts));

        return $base_url;
    }

    public static function isBaseRequestUri() {
        $uri = '/' . trim($_SERVER['REQUEST_URI'],'/');
        return $uri == self::detectBaseUrl();
    }

    public static function isInstallRequestUri() {
        $uri = '/' . trim($_SERVER['REQUEST_URI'],'/');
        return $uri == rtrim(self::detectBaseUrl() ,'/') . '/install';
    }
    
    public static function isMobile() {
        if (self::$_mobile === null) self::$_mobile = new Vendor\Mobile();
        return self::$_mobile->isMobile();
    }
}
