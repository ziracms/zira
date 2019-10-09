<?php
/**
 * Zira project
 * session.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

class Session {
    public static function start() {
        @ini_set('session.use_only_cookies', 1);
        @ini_set('session.cookie_httponly', 1);

        session_name(SESSION_NAME);
        session_start();
    }

    public static function close() {
        session_write_close();
    }

    public static function regenerate() {
        session_regenerate_id(true);
    }

    public static function get($var) {
        if (!isset($_SESSION[$var])) return null;
        return $_SESSION[$var];
    }

    public static function set($var,$val) {
        $_SESSION[$var] = $val;
    }

    public static function remove($var) {
        if (isset($_SESSION[$var])) unset($_SESSION[$var]);
    }

    public static function getArray() {
        return $_SESSION;
    }
}