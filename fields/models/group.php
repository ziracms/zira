<?php
/**
 * Zira project.
 * group.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Models;

use Zira;
use Zira\Orm;

class Group extends Orm {
    public static $table = 'field_groups';
    public static $pk = 'id';
    public static $alias = 'fld_grp';
    
    const WIDGET_CLASS = '\Fields\Widgets\Fields';
    
    public static function getFields() {
        return array(
            'id',
            'title',
            'description',
            'placeholder',
            'category_id',
            'language',
            'sort_order',
            'active',
            'tpl'
        );
    }

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
        return array(
            Zira\Models\Category::getClass() => 'category_id'
        );
    }
    
    public static function getPlaceholders() {
        return array(
            Zira\View::VAR_CONTENT => Zira\Locale::t('Content'),
            Zira\View::VAR_SIDEBAR_LEFT => Zira\Locale::t('Left sidebar'),
            Zira\View::VAR_SIDEBAR_RIGHT => Zira\Locale::t('Right sidebar')
        );
    }
}