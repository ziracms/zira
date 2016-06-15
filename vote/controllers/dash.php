<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Controllers;

use Zira;
use Vote;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Vote\Windows\Votes();
        return new Vote\Models\Votes($window);
    }

    public function install() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('votes');
            $response = $this->getWindowModel()->install($items);
            Zira\Page::render($response);
        }
    }

    public function option() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('vote');
            $content = Zira\Request::post('content');
            $option = Zira\Request::post('option');
            $response = $this->getWindowModel()->option($vote, $content, $option);
            Zira\Page::render($response);
        }
    }

    public function deloptions() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('vote');
            $options = Zira\Request::post('options');
            $response = $this->getWindowModel()->deleteOptions($vote, $options);
            Zira\Page::render($response);
        }
    }

    public function drag() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('item');
            $options = Zira\Request::post('options');
            $orders = Zira\Request::post('orders');
            $response = $this->getWindowModel()->drag($vote, $options, $orders);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('item');
            $response = $this->getWindowModel()->info($vote);
            Zira\Page::render($response);
        }
    }

    public function results() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('item');
            $response = $this->getWindowModel()->results($vote);
            Zira\Page::render($response);
        }
    }
}