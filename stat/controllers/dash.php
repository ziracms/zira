<?php
/**
 * Zira project.
 * dash.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Controllers;

use Zira;
use Stat;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getRequestsWindowModel() {
        $window = new Stat\Windows\Requests();
        return new Stat\Models\Requests($window);
    }

    public function access() {
        if (Zira\Request::isPost()) {
            $vote = Zira\Request::post('item');
            $response = $this->getRequestsWindowModel()->request($vote);
            Zira\Page::render($response);
        }
    }
}