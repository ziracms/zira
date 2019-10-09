<?php
/**
 * Zira project.
 * languages.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Languages extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Languages();
        return new Dash\Models\Languages($window);
    }

    protected function getTranslatesModel() {
        $window = new Dash\Windows\Translates();
        return new Dash\Models\Translates($window);
    }

    public function deactivate() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->deactivate($language);
            Zira\Page::render($response);
        }
    }

    public function activate() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->activate($language);
            Zira\Page::render($response);
        }
    }

    public function setdefault() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->setDefault($language);
            Zira\Page::render($response);
        }
    }
    
    public function setpaneldefault() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->setPanelDefault($language);
            Zira\Page::render($response);
        }
    }

    public function up() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->pickUp($language);
            Zira\Page::render($response);
        }
    }

    public function down() {
        if (Zira\Request::isPost()) {
            $language = Zira\Request::post('language');
            $response = $this->getWindowModel()->pullDown($language);
            Zira\Page::render($response);
        }
    }
    
    public function drag() {
        if (Zira\Request::isPost()) {
            $languages = Zira\Request::post('languages');
            $orders = Zira\Request::post('orders');
            $response = $this->getWindowModel()->drag($languages, $orders);
            Zira\Page::render($response);
        }
    }

    public function addstring() {
        if (Zira\Request::isPost()) {
            $str = Zira\Request::post('string');
            $lang = Zira\Request::post('language');
            $response = $this->getTranslatesModel()->add($str, $lang);
            Zira\Page::render($response);
        }
    }

    public function translate() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('strid');
            $translate = Zira\Request::post('translate');
            $lang = Zira\Request::post('language');
            $response = $this->getTranslatesModel()->translate(intval($id), $translate, $lang);
            Zira\Page::render($response);
        }
    }
}