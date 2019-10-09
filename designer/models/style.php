<?php
/**
 * Zira project.
 * style.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Designer\Models;

use Zira\Orm;
use Zira\Locale;

class Style extends Orm {
    public static $table = 'styles';
    public static $pk = 'id';
    public static $alias = 'stls';
    
    const STATUS_FILTER_RECORD = 'record';
    const STATUS_FILTER_CATEGORY = 'category';
    const STATUS_FILTER_CATEGORY_AND_RECORD = 'category_record';

    public static function getTable() {
        return self::$table;
    }

    public static function getPk() {
        return self::$pk;
    }

    public static function getAlias() {
        return self::$alias;
    }

    public static function getReferences() {
        return array();
    }
    
    public static function getFiltersArray() {
        return array(
            self::STATUS_FILTER_CATEGORY => Locale::t('Display on category page only'),
            self::STATUS_FILTER_RECORD => Locale::t('Display on record page only'),
            self::STATUS_FILTER_CATEGORY_AND_RECORD => Locale::t('Display on category and record pages only')
        );
    }
}