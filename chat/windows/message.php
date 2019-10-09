<?php
/**
 * Zira project.
 * message.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Message extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Chat message';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        $chat_id = (int)Zira\Request::post('chat_id');
        if (!$chat_id) return array('error' => Zira\Locale::t('An error occurred'));
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Chat\Chat::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $chat = new \Chat\Models\Chat($chat_id);
        if (!$chat->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $form = new \Chat\Forms\Message();
        if ($this->item) {
            $message = new \Chat\Models\Message($this->item);
            if (!$message->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($message->toArray());
        } else {
            $form->setValues(array('chat_id'=>$chat_id));
        }

        $this->setTitle(Zira\Locale::t(self::$_title) . ' - '. $chat->title);

        $this->setBodyContent($form);
    }
}