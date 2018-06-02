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
    
    public function delete() {
        if (Zira\Request::isPost()) {
            $menu = Zira\Request::post('menu');
            $response = $this->getWindowModel()->deleteMenu($menu);
            Zira\Page::render($response);
        }
    }
    
    public function info() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('id');
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->info($id, $language);
            Zira\Page::render($response);
        }
    }
}