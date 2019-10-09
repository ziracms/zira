<?php
/**
 * Zira project.
 * comments.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Comments extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Comments();
        return new Dash\Models\Comments($window);
    }

    public function activate() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('items');
            $response = $this->getWindowModel()->activate($items);
            Zira\Page::render($response);
        }
    }

    public function edit() {
        if (Zira\Request::isPost()) {
            $item = Zira\Request::post('item');
            $comment = Zira\Request::post('comment');
            $response = $this->getWindowModel()->edit($item, $comment);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->info($id);
            Zira\Page::render($response);
        }
    }

    public function preview() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->preview($id);
            Zira\Page::render($response);
        }
    }
}