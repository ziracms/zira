<?php
/**
 * Zira project.
 * hook.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira;

class Hook {
    protected static $_hooks = array();

    public static function register($name, $callback) {
        if (!array_key_exists($name, self::$_hooks)) self::$_hooks[$name] = array();
        self::$_hooks[$name][]=$callback;
    }

    public static function run($name, $arg = null) {
        $result = array();
        if (!array_key_exists($name, self::$_hooks)) return $result;
        foreach(self::$_hooks[$name] as $callback) {
            $_result = call_user_func($callback, $arg);
            if ($_result) {
                $result [] = $_result;
            }
        }
        return $result;
    }
}