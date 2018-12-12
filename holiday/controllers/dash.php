<?php
/**
 * Zira project.
 * dash.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Controllers;

use Zira;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new \Holiday\Windows\Holidays();
        return new \Holiday\Models\Holidays($window);
    }
    
    public function preview() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->preview($id);
            Zira\Page::render($response);
        }
    }
}