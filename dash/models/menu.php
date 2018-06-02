<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Menu extends Model {
    const NEW_MENU_ID = 0;
    
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $id) {
            $menuItem = new Zira\Models\Menu($id);
            if (!$menuItem->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

            $menuItem->delete();
            self::deleteRecursive($id);
        }

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    protected static function deleteRecursive($parent_id) {
        $rows = Zira\Models\Menu::getCollection()
                ->where('parent_id','=',$parent_id)
                ->get(null, true);

        if (count($rows)==0) return;

        foreach($rows as $row) {
            $menuItem = new Zira\Models\Menu();
            $menuItem->loadFromArray($row);
            $menuItem->delete();
            self::deleteRecursive($row['id']);
        }
    }

    public function drag($items, $orders) {
        if (empty($items) || !is_array($items) || count($items)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($items)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_items = array();
        $_orders = array();
        foreach($items as $id) {
            $_item = new Zira\Models\Menu($id);
            if (!$_item->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_items []= $_item;
            $_orders []= $_item->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_items as $index=>$item) {
            $item->sort_order = intval($orders[$index]);
            $item->save();
        }

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function drop($item, $type, $menu, $parent) {
        if (empty($item) || empty($type) || empty($menu)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if ($menu != Zira\Menu::MENU_PRIMARY && $menu != Zira\Menu::MENU_SECONDARY && $menu != Zira\Menu::MENU_FOOTER) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $parent = intval($parent);
        if (empty($parent)) {
            $parent = 0;
        } else {
            $_parent = new Zira\Models\Menu($parent);
            if (!$_parent->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        }

        if ($type == 'category') {
            $category = new Zira\Models\Category($item);
            if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $url = $category->name;
            $title = $category->title;
            $language = null;
        } else if ($type == 'record') {
            $record = new Zira\Models\Record($item);
            if (!$record->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            if ($record->category_id != Zira\Category::ROOT_CATEGORY_ID) {
                $category = new Zira\Models\Category($record->category_id);
                if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
                $url = $category->name . '/' . $record->name;
            } else {
                $url = $record->name;
            }
            $title = $record->title;
            $language = $record->language;
        }

        $max_order = Zira\Models\Menu::getCollection()->max('sort_order')->get('mx');

        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = (int)$menu;
        $menuItem->parent_id = (int)$parent;
        $menuItem->sort_order = ++$max_order;
        $menuItem->language = $language;
        $menuItem->url = $url;
        $menuItem->title = $title;
        $menuItem->external = 0;

        $menuItem->save();

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }
    
    public function info($id, $language) {
        $item = new Zira\Models\Menu($id);
        if (!$item->loaded()) return;

        $parent = $item->id;
        $menu = $item->menu_id;

        $query = Zira\Models\Menu::getCollection();

        if (empty($language) || !in_array($language, Zira\Config::get('languages'))) {
            $query->where('parent_id', '=', $parent);
        } else {
            $query->open_query();
            $query->where('parent_id', '=', $parent);
            $query->and_where('language','is',null);
            $query->close_query();
            $query->union();
            $query->open_query();
            $query->where('parent_id', '=', $parent);
            $query->and_where('language','=',$language);
            $query->close_query();
            $query->merge();
        }
        $query->order_by('sort_order', 'asc');

        $rows = $query->get();

        $secondory_items = array();
        $child_items = array();
        foreach($rows as $row) {
            if ($menu == Zira\Menu::MENU_PRIMARY && $row->menu_id == Zira\Menu::MENU_SECONDARY) {
                $secondory_items []= array(
                    'id' => $row->id,
                    'menu_id' => $row->menu_id,
                    'title' => $row->title
                );
            } else {
                $child_items []= array(
                    'id' => $row->id,
                    'menu_id' => $row->menu_id,
                    'title' => $row->title
                );
            }
        }

        return array(
            'secondary' => $secondory_items,
            'child' => $child_items
        );
    }
    
    public static function createNewMenu() {
        $max_id = Zira\Models\Menu::getCollection()->max('menu_id')->get('mx');
        $new_id = ++$max_id;
        $widget = Zira\Models\Widget::getCollection()
                    ->where('name', '=', Zira\Menu::WIDGET_CLASS)
                    ->and_where('params', '=', $new_id)
                    ->get(0, true);
               
        if ($widget) {
            $widgetObj = new Zira\Models\Widget();
            $widgetObj->loadFromArray($widget);
            $widgetObj->active = true;
            $widgetObj->save();
            return $new_id;
        }
        
        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Zira\Menu::WIDGET_CLASS;
        $widget->module = 'zira';
        $widget->placeholder = Zira\View::VAR_SIDEBAR_RIGHT;
        $widget->params = $new_id;
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();
        
        return $new_id;
    }
    
    public function deleteMenu($menu_id) {
        if (empty($menu_id) || !is_numeric($menu_id) || $menu_id == Zira\Menu::MENU_PRIMARY || $menu_id == Zira\Menu::MENU_SECONDARY || $menu_id == Zira\Menu::MENU_FOOTER || $menu_id == self::NEW_MENU_ID) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        Zira\Models\Menu::getCollection()
                ->delete()
                ->where('menu_id','=',$menu_id)
                ->execute();

        Zira\Models\Widget::getCollection()
                ->delete()
                ->where('name','=',Zira\Menu::WIDGET_CLASS)
                ->and_where('params','=',$menu_id)
                ->execute();

        Zira\Cache::clear();

        return array('success'=>1);
    }
}