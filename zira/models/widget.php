<?php
/**
 * Zira project.
 * widget.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Locale;
use Zira\Orm;
use Zira\View;

class Widget extends Orm {
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_FILTER_RECORD = 'record';
    const STATUS_FILTER_CATEGORY = 'category';
    const STATUS_FILTER_CATEGORY_AND_RECORD = 'category_record';

    public static $table = 'widgets';
    public static $pk = 'id';
    public static $alias = 'wgt';

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

    public static function getPlaceholders() {
        return array(
            View::VAR_HEAD_BOTTOM => Locale::t('HEAD tag'),
            View::VAR_BODY_TOP => Locale::t('BODY tag top'),
            View::VAR_HEADER => Locale::t('Header'),
            View::VAR_SIDEBAR_LEFT => Locale::t('Left sidebar'),
            View::VAR_SIDEBAR_RIGHT => Locale::t('Right sidebar'),
            View::VAR_CONTENT_TOP => Locale::t('Before content'),
            View::VAR_CONTENT => Locale::t('Content'),
            View::VAR_CONTENT_BOTTOM => Locale::t('After content'),
            View::VAR_FOOTER => Locale::t('Footer'),
            View::VAR_BODY_BOTTOM => Locale::t('BODY tag bottom')
        );
    }

    public static function getFiltersArray() {
        return array(
            self::STATUS_FILTER_CATEGORY => Locale::t('Display on category page only'),
            self::STATUS_FILTER_RECORD => Locale::t('Display on record page only'),
            self::STATUS_FILTER_CATEGORY_AND_RECORD => Locale::t('Display on category and record pages only')
        );
    }
}