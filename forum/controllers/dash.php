<?php
/**
 * Zira project.
 * dash.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Controllers;

use Zira;
use Forum;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getCategoriesWindowModel() {
        $window = new Forum\Windows\Categories();
        return new Forum\Models\Categories($window);
    }

    protected function getForumsWindowModel() {
        $window = new Forum\Windows\Forums();
        return new Forum\Models\Forums($window);
    }

    public function dragcategory() {
        if (Zira\Request::isPost()) {
            $categories = Zira\Request::post('categories');
            $orders = Zira\Request::post('orders');
            $response = $this->getCategoriesWindowModel()->drag($categories, $orders);
            Zira\Page::render($response);
        }
    }

    public function dragforum() {
        if (Zira\Request::isPost()) {
            $forums = Zira\Request::post('forums');
            $orders = Zira\Request::post('orders');
            $response = $this->getForumsWindowModel()->drag($forums, $orders);
            Zira\Page::render($response);
        }
    }
}