<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Config;
use Zira\Locale;
use Zira\Orm;
use Zira\View;

class Category extends Orm {
    const WIDGET_CLASS = '\Zira\Widgets\Category';
    const WIDGET_PLACEHOLDER = View::VAR_SIDEBAR_RIGHT;

    public static $table = 'categories';
    public static $pk = 'id';
    public static $alias = 'cat';

    const REGEXP_NAME = '/^[a-zа-яё0-9_-]*[a-zа-яё]+[a-zа-яё0-9_-]*$/u';

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

    public static function getHomeCategories() {
        $top_categories = self::getTopCategories();
        try {
            $home_categories_map = array();
            foreach ($top_categories as $top_category) {
                $top_category->sort_order = 999999;
                $home_categories_map[$top_category->id] = $top_category;
            }
            $home_categories = Config::get('home_categories');
            if (!$home_categories) return $top_categories;
            $home_categories_parts = explode(',', $home_categories);
            for ($i = 0; $i < count($home_categories_parts); $i++) {
                $id = intval($home_categories_parts[$i]);
                if (array_key_exists($id, $home_categories_map)) {
                    $home_categories_map[$id]->sort_order = $i;
                }
            }
            usort($top_categories, array(self::getClass(), 'sortHomeCategories'));
        } catch(\Exception $e) {
            // ignore
        }
        return $top_categories;
    }

    public static function sortHomeCategories($a, $b) {
        if ($a->sort_order == $b->sort_order) return 0;
        else return ($a->sort_order < $b->sort_order) ? -1 : 1;
    }
}