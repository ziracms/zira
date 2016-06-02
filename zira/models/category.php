<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Locale;
use Zira\Orm;
use Zira\View;

class Category extends Orm {
    const WIDGET_CLASS = '\Zira\Widgets\Category';
    const WIDGET_PLACEHOLDER = View::VAR_SIDEBAR_RIGHT;

    public static $table = 'categories';
    public static $pk = 'id';
    public static $alias = 'cat';

    const REGEXP_NAME = '/^[a-zа-я]+[a-zа-я0-9_-]*$/u';

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

    public static function getArray($_prefix = ' - ') {
        $rows = self::getCollection()->order_by('name','asc')->get();

        $categories = array();
        foreach($rows as $row) {
            $prefix = '';
            if (!empty($_prefix) && strpos($row->name,'/')!==false) {
                $prefix = str_repeat($_prefix, strlen(preg_replace('/[^\/]/','',$row->name)));
            }
            $categories[$row->id] = $prefix.Locale::t($row->title);
        }

        return $categories;
    }

    public static function getTopCategories($order_by = 'id', $sort = 'asc') {
        return self::getCollection()
                                ->where('parent_id', '=', \Zira\Category::ROOT_CATEGORY_ID)
                                ->order_by($order_by, $sort)
                                ->get();
    }

    public static function getChildCategories($category) {
        return self::getCollection()
                                ->where('name', 'like', $category->name . '/%')
                                ->get();
    }
}