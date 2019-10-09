<?php
/**
 * Zira project.
 * dash.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Featured\Controllers;

use Zira;
use Featured;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Featured\Windows\Featured();
        return new Featured\Models\Dash($window);
    }

    public function drag() {
        if (Zira\Request::isPost()) {
            $records = Zira\Request::post('records');
            $orders = Zira\Request::post('orders');
            $response = $this->getWindowModel()->drag($records, $orders);
            Zira\Page::render($response);
        }
    }

    public function add() {
        if (Zira\Request::isPost()) {
            $record = Zira\Request::post('record');
            $response = $this->getWindowModel()->add($record);
            Zira\Page::render($response);
        }
    }
}