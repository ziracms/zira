<?php
/**
 * Zira project.
 * dash.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Controllers;

use Zira;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new \Push\Windows\Push();
        return new \Push\Models\Push($window);
    }
    
    public function generate() {
        if (Zira\Request::isPost()) {
            $response = $this->getWindowModel()->generateKeys();
            Zira\Page::render($response);
        }
    }

    public function send() {
        if (Zira\Request::isPost()) {
            $response = $this->getWindowModel()->send();
            Zira\Page::render($response);
        }
    }
}