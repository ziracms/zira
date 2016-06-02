<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira;

class Category {
    const ROOT_CATEGORY_ID = 0;
    const CACHE_KEY = 'categories';
    protected static $_chain = array();
    protected static $_current;
    protected static $_param;
    protected static $_childs;

    public static function load($request) {
        $request = trim($request,'/');
        if (empty($request)) return;

        if (CACHE_CATEGORIES_LIST) {
            $rows = self::loadAll($request);
        } else {
            $rows = self::loadPartial($request);
        }

        $p = strrpos($request,'/');
        $p_request = substr($request,0,$p);

        foreach($rows as $row) {
            self::$_chain []= $row;
            Page::addBreadcrumb($row->name, Locale::t($row->title));
            if ($row->name == $request) {
                self::$_current = $row;
                self::$_param = null;
            }
            if ($p !== false && $p_request && $row->name != $request && $row->name == $p_request) {
                self::$_current = $row;
                self::$_param = substr($request, $p + 1);
            }
        }
    }

    public static function loadPartial($request) {
        $parts = explode('/',$request);

        if (count($parts)>1) {
            $_request = $parts[0];
            $query = Models\Category::getCollection()
                ->open_query()
                ->where('name','=',$_request);
            for($i=1;$i<count($parts);$i++) {
                $_request .= '/'.$parts[$i];
                $query->close_query();
                $query->union();
                $query->open_query();
                $query->where('name','=',$_request);
            }
            $query->close_query();
            $rows = $query->get();
        } else {
            $rows = Models\Category::getCollection()
                            ->where('name','=',$request)
                            ->get();
        }

        return $rows;
    }

    public static function getAllCategories() {
        $rows = Cache::getArray(self::CACHE_KEY);
        if ($rows===false) {
            $rows = Models\Category::getCollection()->order_by('id','asc')->get();
            Cache::setArray(self::CACHE_KEY, $rows);
        }
        return $rows;
    }

    public static function loadAll($request) {
        $parts = explode('/',$request);

        $rows = self::getAllCategories();

        self::$_childs = array();
        $_rows = array();
        if (count($parts)>1) {
            $map = array();
            foreach($rows as $row) {
                $map[$row->name] = $row;
                if (strpos($row->name, $request.'/')===0) {
                    self::$_childs []= $row;
                }
            }
            $_request = '';
            for($i=0;$i<count($parts);$i++) {
                if (!empty($_request)) $_request .= '/';
                $_request .= $parts[$i];
                if (array_key_exists($_request, $map)) {
                    $_rows []= $map[$_request];
                }
            }
        } else {
            foreach($rows as $row) {
                if ($row->name == $request) {
                    $_rows []= $row;
                }
                if (strpos($row->name, $request.'/')===0) {
                    self::$_childs []= $row;
                }
            }
        }

        return $_rows;
    }

    public static function current() {
        return self::$_current;
    }

    public static function param() {
        return self::$_param;
    }

    public static function chain() {
        return self::$_chain;
    }

    public static function setCurrent($category) {
        self::$_current = $category;
    }

    public static function setChilds($childs) {
        self::$_childs = $childs;
    }

    public static function getChainIdsArray() {
        $ids = array();
        foreach(self::$_chain as $row) {
            $ids []= $row->id;
        }
        return $ids;
    }

    public static function currentChilds() {
        return self::$_childs;
    }

    public static function getChilds($category = null) {
        return Models\Category::getChildCategories($category);
    }

    public static function getCategoriesMap() {
        $categories = self::getAllCategories();
        if (empty($categories)) return array();
        $child_counts = array();
        foreach($categories as $category) {
            if (!array_key_exists($category->parent_id, $child_counts)) {
                $child_counts[$category->parent_id] = 0;
            }
            $child_counts[$category->parent_id]++;
        }
        $items = array();
        self::buildCategoriesMap($items, $categories, $child_counts);
        return $items;
    }

    protected static function buildCategoriesMap(array &$items, array &$categories, array &$child_counts, $parent_id = 0) {
        foreach($categories as $category) {
            if ($category->parent_id == $parent_id) {
                $items []= $category;
                if (array_key_exists($category->id, $child_counts) && $child_counts[$category->id]>0) {
                    self::buildCategoriesMap($items, $categories, $child_counts, $category->id);
                }
            }
        }
    }
}