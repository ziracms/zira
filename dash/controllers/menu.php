<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Menu extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Menu();
        return new Dash\Models\Menu($window);
    }

    public function drag() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('items');
            $orders = Zira\Request::post('orders');
            $response = $this->getWindowModel()->drag($items, $orders);
            Zira\Page::render($response);
        }
    }

    public function drop() {
        if (Zira\Request::isPost()) {
            $item = Zira\Request::post('item');
            $type = Zira\Request::post('type');
            $menu = Zira\Request::post('menu');
            $parent = Zira\Request::post('parent');
            $response = $this->getWindowModel()->drop($item, $type, $menu, $parent);
            Zira\Page::render($response);
        }
    }
    
    public function info() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('id');
            $language = Zira\Request::post('language');
        
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
                if ($row->menu_id == Zira\Menu::MENU_SECONDARY) {
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
            
            Zira\Page::render([
                'secondary' => $secondory_items,
                'child' => $child_items
            ]);
        }
    }
}