<?php
/**
 * Zira project.
 * field.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Db\Implement;

interface Field {
    /**
     * Tinyint type
     * @param bool|false $not_null
     * @param bool|false $unsigned
     * @param null $default
     * @return string
     */
    public static function tinyint($not_null = false, $unsigned = false, $default = null);

    /**
    * Smallint type
    * @param bool|false $not_null
    * @param bool|false $unsigned
    * @param null $default
    * @return string
    */
    public static function smallint($not_null = false, $unsigned = false, $default = null);
    
    /**
     * Integer type
     * @param bool|false $not_null
     * @param bool|false $unsigned
     * @param null $default
     * @return string
     */
    public static function int($not_null = false, $unsigned = false, $default = null);

    /**
     * Date type
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function date($not_null = false, $default = null);

    /**
     * Datetime type
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function datetime($not_null = false, $default = null);

    /**
     * Short string type (255 chars)
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function string($not_null = false, $default = null);

    /**
     * Text type
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function text($not_null = false, $default = null);

    /**
     * Long text type
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function longtext($not_null = false, $default = null);
    
    /**
     * Blob type
     * @param bool|false $not_null
     * @param null $default
     * @return string
     */
    public static function blob($not_null = false, $default = null);
}