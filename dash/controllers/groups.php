<?php
/**
 * Zira project.
 * groups.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Groups extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Groups();
        return new Dash\Models\Groups($window);
    }

    public function deactivate() {
        if (Zira\Request::isPost()) {
            $groups = Zira\Request::post('groups');
            $response = $this->getWindowModel()->deactivate($groups);
            Zira\Page::render($response);
        }
    }

    public function activate() {
        if (Zira\Request::isPost()) {
            $groups = Zira\Request::post('groups');
            $response = $this->getWindowModel()->activate($groups);
            Zira\Page::render($response);
        }
    }

    public function rename() {
        if (Zira\Request::isPost()) {
            $group_id = Zira\Request::post('group_id');
            $name = Zira\Request::post('name');
            $response = $this->getWindowModel()->rename(intval($group_id), $name);
            Zira\Page::render($response);
        }
    }

    public function create() {
        if (Zira\Request::isPost()) {
            $name = Zira\Request::post('name');
            $response = $this->getWindowModel()->createGroup( $name);
            Zira\Page::render($response);
        }
    }

    public function delete() {
        if (Zira\Request::isPost()) {
            $groups = Zira\Request::post('items');
            $response = $this->getWindowModel()->deleteGroups($groups);
            Zira\Page::render($response);
        }
    }
}