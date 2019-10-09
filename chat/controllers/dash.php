<?php
/**
 * Zira project.
 * dash.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Controllers;

use Zira;
use Chat;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }
    
    protected function getChatWindowModel() {
        $window = new Chat\Windows\Chats();
        return new Chat\Models\Chats($window);
    }

    protected function getMessagesWindowModel() {
        $window = new Chat\Windows\Messages();
        return new Chat\Models\Messages($window);
    }

    public function preview() {
        if (Zira\Request::isPost()) {
            $item = Zira\Request::post('item');
            $response = $this->getMessagesWindowModel()->preview(intval($item));
            Zira\Page::render($response);
        }
    }
    
    public function install() {
        if (Zira\Request::isPost()) {
            $chats = Zira\Request::post('chats');
            $response = $this->getChatWindowModel()->install($chats);
            Zira\Page::render($response);
        }
    }
}