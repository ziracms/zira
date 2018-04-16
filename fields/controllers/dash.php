<?php
/**
 * Zira project.
 * dash.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Controllers;

use Zira;
use Fields;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getFieldsWindowModel() {
        $window = new Fields\Windows\Fields();
        return new Fields\Models\Fields($window);
    }
    
    protected function getGroupsWindowModel() {
        $window = new Fields\Windows\Groups();
        return new Fields\Models\Groups($window);
    }

    public function groupdrag() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('items');
            $orders = Zira\Request::post('orders');
            $response = $this->getGroupsWindowModel()->drag($items, $orders);
            Zira\Page::render($response);
        }
    }
    
    public function fielddrag() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('items');
            $orders = Zira\Request::post('orders');
            $response = $this->getFieldsWindowModel()->drag($items, $orders);
            Zira\Page::render($response);
        }
    }
}