<?php
/**
 * Zira project.
 * dash.php
 * (c)2018 https://github.com/ziracms/zira
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
            $item = Zira\Request::post('item');
            $response = $this->getRequestsWindowModel()->request($item);
            Zira\Page::render($response);
        }
    }
    
    public function clean() {
        if (Zira\Request::isPost()) {
            try {
                Stat\Models\Access::cleanUp();
                $response = array('message'=>Zira\Locale::t('Statistics cleaned up', 'stat'));
            } catch(\Exception $err) {
                $response = array('error'=>Zira\Locale::t('An error occurred'));
            }
            Zira\Page::render($response);
        }
    }
}