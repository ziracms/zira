<?php
/**
 * Zira project.
 * table.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Db\Mysql;

abstract class Field implements \Zira\Db\Implement\Field {
    const FIELD_TYPE_TINYINT = 'TINYINT(4)';
    const FIELD_TYPE_TINYINT_NOT_NULL = 'TINYINT(4) NOT NULL';
    const FIELD_TYPE_TINYINT_UNSIGNED = 'TINYINT(3) UNSIGNED';
    const FIELD_TYPE_TINYINT_UNSIGNED_NOT_NULL = 'TINYINT(3) UNSIGNED NOT NULL';
    const FIELD_TYPE_SMALLINT = 'SMALLINT(6)';
    const FIELD_TYPE_SMALLINT_NOT_NULL = 'SMALLINT(6) NOT NULL';
    const FIELD_TYPE_SMALLINT_UNSIGNED = 'SMALLINT(5) UNSIGNED';
    const FIELD_TYPE_SMALLINT_UNSIGNED_NOT_NULL = 'SMALLINT(5) UNSIGNED NOT NULL';
    const FIELD_TYPE_INT = 'INT(11)';
    const FIELD_TYPE_INT_NOT_NULL = 'INT(11) NOT NULL';
    const FIELD_TYPE_INT_UNSIGNED = 'INT(10) UNSIGNED';
    const FIELD_TYPE_INT_UNSIGNED_NOT_NULL = 'INT(10) UNSIGNED NOT NULL';
    const FIELD_TYPE_PRIMARY = 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT';
    const FIELD_TYPE_DATE = 'DATE';
    const FIELD_TYPE_DATE_NOT_NULL = 'DATE NOT NULL';
    const FIELD_TYPE_DATETIME = 'DATETIME';
    const FIELD_TYPE_DATETIME_NOT_NULL = 'DATETIME NOT NULL';
    const FIELD_TYPE_STRING = 'VARCHAR(255)';
    const FIELD_TYPE_STRING_NOT_NULL = 'VARCHAR(255) NOT NULL';
    const FIELD_TYPE_TEXT = 'TEXT';
    const FIELD_TYPE_TEXT_NOT_NULL = 'TEXT NOT NULL';
    const FIELD_TYPE_LONGTEXT = 'LONGTEXT';
    const FIELD_TYPE_LONGTEXT_NOT_NULL = 'LONGTEXT NOT NULL';
    const FIELD_TYPE_BLOB = 'BLOB';
    const FIELD_TYPE_BLOB_NOT_NULL = 'BLOB NOT NULL';

    protected static function buildDefault($default) {
        if ($default !== null) {
            return ' DEFAULT '.Db::escape($default);
        } else {
            return '';
        }
    }

    public static function primary() {
        return self::FIELD_TYPE_PRIMARY;
    }

    public static function tinyint($not_null = false, $unsigned = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null && $unsigned) {
            return self::FIELD_TYPE_TINYINT_UNSIGNED_NOT_NULL . $default;
        } else if ($not_null) {
            return self::FIELD_TYPE_TINYINT_NOT_NULL . $default;
        } else if ($unsigned) {
            return self::FIELD_TYPE_TINYINT_UNSIGNED . $default;
        } else {
            return self::FIELD_TYPE_TINYINT . $default;
        }
    }

    public static function smallint($not_null = false, $unsigned = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null && $unsigned) {
            return self::FIELD_TYPE_SMALLINT_UNSIGNED_NOT_NULL . $default;
        } else if ($not_null) {
            return self::FIELD_TYPE_SMALLINT_NOT_NULL . $default;
        } else if ($unsigned) {
            return self::FIELD_TYPE_SMALLINT_UNSIGNED . $default;
        } else {
            return self::FIELD_TYPE_SMALLINT . $default;
        }
    }

    public static function int($not_null = false, $unsigned = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null && $unsigned) {
            return self::FIELD_TYPE_INT_UNSIGNED_NOT_NULL . $default;
        } else if ($not_null) {
            return self::FIELD_TYPE_INT_NOT_NULL . $default;
        } else if ($unsigned) {
            return self::FIELD_TYPE_INT_UNSIGNED . $default;
        } else {
            return self::FIELD_TYPE_INT . $default;
        }
    }

    public static function date($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_DATE_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_DATE . $default;
        }
    }

    public static function datetime($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_DATETIME_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_DATETIME . $default;
        }
    }

    public static function string($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_STRING_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_STRING . $default;
        }
    }

    public static function text($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_TEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TEXT . $default;
        }
    }

    public static function longtext($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_LONGTEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_LONGTEXT . $default;
        }
    }
    
    public static function blob($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_BLOB_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_BLOB . $default;
        }
    }
}