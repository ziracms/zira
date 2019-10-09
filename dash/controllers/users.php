<?php
/**
 * Zira project.
 * users.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Users extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Users();
        return new Dash\Models\Users($window);
    }

    public function noimage() {
        if (Zira\Request::isPost()) {
            $user_id = Zira\Request::post('user_id');
            $response = $this->getWindowModel()->deleteAvatar(intval($user_id));
            Zira\Page::render($response);
        }
    }

    public function deactivate() {
        if (Zira\Request::isPost()) {
            $users = Zira\Request::post('users');
            $response = $this->getWindowModel()->deactivate($users);
            Zira\Page::render($response);
        }
    }

    public function activate() {
        if (Zira\Request::isPost()) {
            $users = Zira\Request::post('users');
            $response = $this->getWindowModel()->activate($users);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $user_id = Zira\Request::post('user_id');
            $response = $this->getWindowModel()->info(intval($user_id));
            Zira\Page::render($response);
        }
    }
}