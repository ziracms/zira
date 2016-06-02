<?php
/**
 * Zira project.
 * datetime.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira;

class Datetime {
    protected static $_dateTimeObject;

    public static function init() {
        $timezone = Config::get('timezone');
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        } else if (!@ini_get('date.timezone')) {
            date_default_timezone_set(DEFAULT_TIMEZONE);
        }
    }

    public static function getDateTimeObject() {
        if (self::$_dateTimeObject===null) {
            self::$_dateTimeObject = new \DateTime();
        }
        return self::$_dateTimeObject;
    }

    public static function getOffset() {
        return self::getDateTimeObject()->getOffset();
    }

    public static function getOffsetTime() {
        return time() + self::getOffset();
    }
}