<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira;

class Menu {
    const MENU_PRIMARY = 1;
    const MENU_SECONDARY = 2;
    const MENU_FOOTER = 3;

    const CACHE_KEY = 'menu';
    const USER_MENU_HOOK_NAME = 'zira_user_menu';

    protected static $_initialized = false;
    protected static $_secondary_initialized = false;
    protected static $_primary_items = array();
    protected static $_primary_dropdowns = array();
    protected static $_secondary_items = array();
    protected static $_secondary_dropdowns = array();
    protected static $_footer_items = array();
    protected static $_footer_dropdowns = array();
    protected static $_primary_active_url = null;
    protected static $_secondary_active_url = null;
    protected static $_footer_active_url = null;
    protected static $_secondary_parent_id = 0;

    public static function init() {
        if (self::$_initialized) return;
        $cache_key = self::CACHE_KEY . '.' . Locale::getLanguage();
        $items = Cache::getArray($cache_key);

        if ($items === false) {
            $items = Models\Menu::getCollection()
                ->open_query()
                ->where('menu_id', '=', self::MENU_PRIMARY)
                ->and_where('language', 'is', null)
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->union()
                ->open_query()
                ->where('menu_id', '=', self::MENU_PRIMARY)
                ->and_where('language', '=', Locale::getLanguage())
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->union()
                ->open_query()
                ->where('menu_id', '=', self::MENU_FOOTER)
                ->and_where('language', 'is', null)
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->union()
                ->open_query()
                ->where('menu_id', '=', self::MENU_FOOTER)
                ->and_where('language', '=', Locale::getLanguage())
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->get();

            usort($items, array(Models\Menu::getClass(), 'sortAsc'));

            Cache::setArray($cache_key, $items);
        }

        foreach($items as $item) {
            if ($item->menu_id == self::MENU_PRIMARY) {
                if ($item->active == Models\Menu::STATUS_ACTIVE) {
                    if ($item->parent_id > 0) {
                        if (!array_key_exists($item->parent_id, self::$_primary_dropdowns)) {
                            self::$_primary_dropdowns[$item->parent_id] = array();
                        }
                        self::$_primary_dropdowns[$item->parent_id][] = $item;
                    } else {
                        self::$_primary_items [] = $item;
                    }
                }

                if (self::isURLActive($item->url) && (
                    !self::$_primary_active_url ||
                    mb_strlen($item->url, CHARSET)>mb_strlen(self::$_primary_active_url, CHARSET)
                )) {
                    if ($item->active == Models\Menu::STATUS_ACTIVE) {
                        self::$_primary_active_url = $item->url;
                    }
                    self::$_secondary_parent_id = $item->id;
                }
                if (self::$_primary_active_url &&
                    !self::$_secondary_parent_id &&
                    $item->url == self::$_primary_active_url
                ) {
                    self::$_secondary_parent_id = $item->id;
                }
            } else if ($item->menu_id == self::MENU_FOOTER && $item->active == Models\Menu::STATUS_ACTIVE) {
                if ($item->parent_id > 0) {
                    if (!array_key_exists($item->parent_id, self::$_footer_dropdowns)) {
                        self::$_footer_dropdowns[$item->parent_id] = array();
                    }
                    self::$_footer_dropdowns[$item->parent_id][] = $item;
                } else {
                    self::$_footer_items [] = $item;
                }

                if (self::isURLActive($item->url) && (
                    !self::$_footer_active_url ||
                    mb_strlen($item->url, CHARSET)>mb_strlen(self::$_footer_active_url, CHARSET)
                )) {
                    self::$_footer_active_url = $item->url;
                }
            }
        }

        self::$_initialized = true;
    }

    public static function getPrimaryMenuItems() {
        if (!self::$_initialized) self::init();
        return self::$_primary_items;
    }

    public static function getPrimaryMenuItemDropdown($id) {
        if (array_key_exists($id, self::$_primary_dropdowns)) {
            return self::$_primary_dropdowns[$id];
        } else {
            return array();
        }
    }

    public static function getFooterMenuItems() {
        if (!self::$_initialized) self::init();
        return self::$_footer_items;
    }

    public static function getFooterMenuItemDropdown($id) {
        if (array_key_exists($id, self::$_footer_dropdowns)) {
            return self::$_footer_dropdowns[$id];
        } else {
            return array();
        }
    }

    public static function initSecondaryMenuItems($parent_id, $force = false) {
        if (self::$_secondary_initialized && !$force) return;
        self::$_secondary_initialized = true;
        $items = Models\Menu::getCollection()
                ->open_query()
                ->where('menu_id', '=', self::MENU_SECONDARY)
                ->and_where('parent_id','=',$parent_id)
                ->and_where('language', 'is', null)
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->union()
                ->open_query()
                ->where('menu_id', '=', self::MENU_SECONDARY)
                ->and_where('parent_id','=',$parent_id)
                ->and_where('language', '=', Locale::getLanguage())
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->get();

        if (count($items)==0) return;

        usort($items, array(Models\Menu::getClass(), 'sortAsc'));

        $parents = array();
        foreach($items as $item) {
            if ($item->active != Models\Menu::STATUS_ACTIVE) continue;
            $parents []= $item->id;
            self::$_secondary_items []= $item;

            if (self::isURLActive($item->url) && (
                !self::$_secondary_active_url ||
                mb_strlen($item->url, CHARSET)>mb_strlen(self::$_secondary_active_url, CHARSET)
            )) {
                self::$_secondary_active_url = $item->url;
            }
        }

        if (count($parents)==0) return;

        $items = Models\Menu::getCollection()
                ->open_query()
                ->where('menu_id', '=', self::MENU_SECONDARY)
                ->and_where('parent_id','in',$parents)
                ->and_where('language', 'is', null)
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->union()
                ->open_query()
                ->where('menu_id', '=', self::MENU_SECONDARY)
                ->and_where('parent_id','in',$parents)
                ->and_where('language', '=', Locale::getLanguage())
                ->order_by('sort_order', 'asc')
                ->close_query()
                ->get();

        usort($items, array(Models\Menu::getClass(), 'sortAsc'));

        foreach($items as $item) {
            if ($item->active != Models\Menu::STATUS_ACTIVE) continue;
            if (!array_key_exists($item->parent_id, self::$_secondary_dropdowns)) {
                self::$_secondary_dropdowns[$item->parent_id] = array();
            }
            self::$_secondary_dropdowns[$item->parent_id][]=$item;

            if (self::isURLActive($item->url) && (
                !self::$_secondary_active_url ||
                mb_strlen($item->url, CHARSET)>mb_strlen(self::$_secondary_active_url, CHARSET)
            )) {
                self::$_secondary_active_url = $item->url;
            }
        }
    }

    public static function getSecondaryMenuItems() {
        return self::$_secondary_items;
    }

    public static function getSecondaryMenuItemDropdown($id) {
        if (array_key_exists($id, self::$_secondary_dropdowns)) {
            return self::$_secondary_dropdowns[$id];
        } else {
            return array();
        }
    }

    public static function isSysURL($url) {
        if (empty($url) || $url == '/') return true;
        if ($url=='javascript:void(0)' || substr($url, 0, 1) == '#') return false;
        if (strpos($url, 'http')===0 || substr($url, 0, 1) == '/') return false;
        return true;
    }

    public static function parseURL($url, $encode = true) {
        if (!self::isSysURL($url)) {
            return $url;
        } else {
            if ($encode) $url = Page::encodeURL($url);
            return Helper::url($url);
        }
    }

    public static function isURLActive($url) {
        if (!self::isSysURL($url)) return false;
        if (Router::getModule()==DEFAULT_MODULE &&
            Router::getController()==DEFAULT_CONTROLLER &&
            Router::getAction()==DEFAULT_ACTION
        ) {
            if ($url == '/') return true;
            else return false;
        }
        $record_url = Page::getRecordUrl();
        if ($record_url && $record_url == Page::encodeURL($url)) return true;
        $categories = Category::chain();
        foreach($categories as $category) {
            if ($category->name == $url) return true;
        }
        return $url == Router::getRequest();
    }

    public static function getPrimaryMenuActiveURL() {
        if (!self::$_initialized) self::init();
        return self::$_primary_active_url;
    }

    public static function getFooterMenuActiveURL() {
        if (!self::$_initialized) self::init();
        return self::$_footer_active_url;
    }

    public static function getSecondaryMenuActiveURL() {
        return self::$_secondary_active_url;
    }

    public static function setPrimaryMenuActiveURL($url) {
        self::$_primary_active_url = $url;
    }

    public static function setFooterMenuActiveURL($url) {
        self::$_footer_active_url = $url;
    }

    public static function setSecondaryMenuActiveURL($url) {
        self::$_secondary_active_url = $url;
    }

    public static function getSecondaryParentId() {
        if (!self::$_initialized) self::init();
        return self::$_secondary_parent_id;
    }

    public static function setActiveUrl($url) {
        self::setPrimaryMenuActiveURL($url);
        self::setFooterMenuActiveURL($url);
        self::setSecondaryMenuActiveURL($url);
    }
}