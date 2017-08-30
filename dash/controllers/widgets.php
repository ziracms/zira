<?php
/**
 * Zira project.
 * widgets.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Widgets extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Widgets();
        return new Dash\Models\Widgets($window);
    }

    public function deactivate() {
        if (Zira\Request::isPost()) {
            $widgets = Zira\Request::post('widgets');
            $response = $this->getWindowModel()->deactivate($widgets);
            Zira\Page::render($response);
        }
    }

    public function activate() {
        if (Zira\Request::isPost()) {
            $widgets = Zira\Request::post('widgets');
            $response = $this->getWindowModel()->activate($widgets);
            Zira\Page::render($response);
        }
    }

    public function sort() {
        if (Zira\Request::isPost()) {
            $widgets = Zira\Request::post('widgets');
            $response = $this->getWindowModel()->sort($widgets);
            Zira\Page::render($response);
        }
    }

    public function drag() {
        if (Zira\Request::isPost()) {
            $widgets = Zira\Request::post('widgets');
            $orders = Zira\Request::post('orders');
            $response = $this->getWindowModel()->drag($widgets, $orders);
            Zira\Page::render($response);
        }
    }

    public function copy() {
        if (Zira\Request::isPost()) {
            $widget = Zira\Request::post('widget');
            $response = $this->getWindowModel()->copy($widget);
            Zira\Page::render($response);
        }
    }

    public function block() {
        if (Zira\Request::isPost()) {
            $path = Zira\Request::post('path');
            $response = $this->getWindowModel()->createBlock($path);
            Zira\Page::render($response);
        }
    }
    
    public function autocompletepage() {
        if (Zira\Request::isPost()) {
            $search = Zira\Request::post('search');
            $response = $this->getWindowModel()->autoCompletePage($search);
            Zira\Page::render($response);
        }
    }
}