<?php
/**
 * Zira project
 * config.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

use Zira\Models\Option;

class Config {
    protected static $_sys_defaults = array();
    protected static $_data;

    public static function setSystemDefaults(array $defaults) {
        self::$_sys_defaults = $defaults;
    }

    public static function getSystemDefaults() {
        return self::$_sys_defaults;
    }

    public static function load() {
        self::$_data = self::getSystemDefaults();
        if (ENABLE_CONFIG_DATABASE) {
            $user_configs = Models\Option::getCollection()->get();
            foreach ($user_configs as $user_config) {
                self::$_data[$user_config->name] = $user_config->value;
            }
            self::convertStringArrays();
        }
    }

    public static function convertStringArrays() {
        foreach(self::$_sys_defaults as $name=>$value) {
            if (!is_array($value)) continue;
            if (isset(self::$_data[$name]) && !is_array(self::$_data[$name])) {
                self::$_data[$name] = Option::convertStringToArray(self::$_data[$name]);
            }
        }
    }

    public static function get($param, $default = null) {
        if (!isset(self::$_data[$param])) return $default;
        return self::$_data[$param];
    }

    public static function set($param,$value) {
        self::$_data[$param] = $value;
    }

    public static  function getArray() {
        return self::$_data;
    }
}