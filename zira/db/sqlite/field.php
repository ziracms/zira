<?php
/**
 * Zira project.
 * field.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Db\Sqlite;

abstract class Field implements \Zira\Db\Implement\Field {
    const FIELD_TYPE_TINYINT = 'INTEGER';
    const FIELD_TYPE_TINYINT_NOT_NULL = 'INTEGER NOT NULL';
    const FIELD_TYPE_INT = 'INTEGER';
    const FIELD_TYPE_INT_NOT_NULL = 'INTEGER NOT NULL';
    const FIELD_TYPE_PRIMARY = 'INTEGER PRIMARY KEY';
    const FIELD_TYPE_TEXT = 'TEXT';
    const FIELD_TYPE_TEXT_NOT_NULL = 'TEXT NOT NULL';
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
        if ($not_null) {
            return self::FIELD_TYPE_TINYINT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TINYINT . $default;
        }
    }

    public static function int($not_null = false, $unsigned = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_INT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_INT . $default;
        }
    }

    public static function date($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_TEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TEXT . $default;
        }
    }

    public static function datetime($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_TEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TEXT . $default;
        }
    }

    public static function string($not_null = false, $default = null) {
        $default = self::buildDefault($default);
        if ($not_null) {
            return self::FIELD_TYPE_TEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TEXT . $default;
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
            return self::FIELD_TYPE_TEXT_NOT_NULL . $default;
        } else {
            return self::FIELD_TYPE_TEXT . $default;
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