<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Chat extends Widget {
    protected $_title = 'Chat';
    protected static $_titles;

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(false);
        $this->setPlaceholder(View::VAR_SIDEBAR_RIGHT);
    }
    
    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = \Chat\Models\Chat::getCollection()->get();
            foreach($rows as $row) {
                self::$_titles[$row->id] = $row->title;
            }
        }
        return self::$_titles;
    }

    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            $titles = $this->getTitles();
            if (empty($titles) || !array_key_exists($this->getData(), $titles)) return parent::getTitle();
            return Zira\Locale::tm('Chat','chat') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }

    protected function _render() {
        $id = $this->getData();
        if (!is_numeric($id)) return;
        
        $chat = new \Chat\Models\Chat($id);
        if (!$chat->loaded()) return;
        
        Zira\View::renderView(array(
            'chat' => $chat
        ),'chat/widgets/chat');
    }
}