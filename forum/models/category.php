<?php
/**
 * Zira project.
 * category.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Category extends Orm {
    public static $table = 'forum_categories';
    public static $pk = 'id';
    public static $alias = 'frm_cat';

    public static function getFields() {
        return array(
            'id',
            'title',
            'description',
            'layout',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'access_check',
            'sort_order',
            'tpl',
            'language'
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
        return array();
    }

    public static function getCategories() {
        return self::getCollection()
                            ->order_by('sort_order', 'asc')
                            ->get();
    }

    public static function generateUrl($category) {
        $id = is_numeric($category) ? intval($category) : $category->id;
        return \Forum\Forum::ROUTE . '/group/' . $id;
    }
}