<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Chat extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Chat';

    //protected $_help_url = 'zira/help/chat';

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
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Chat\Chat::PERMISSION_MODERATE)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Chat\Forms\Chat();
        if (!empty($this->item)) {
            $chat = new \Chat\Models\Chat($this->item);
            if (!$chat->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $form->setValues($chat->toArray());
            $this->setTitle(Zira\Locale::tm(self::$_title,'chat').' - '.$chat->title);
        } else {
            $form->setValues(array(
                'refresh_delay'=>\Chat\Chat::DEFAULT_DELAY,
                'placeholder'=>\Chat\Chat::WIDGET_PLACEHOLDER
            ));
            $this->setTitle(Zira\Locale::tm('New chat','chat'));
        }

        $this->setBodyContent($form);
    }
}