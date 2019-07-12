<?php
/**
 * Zira project.
 * settings.php
 * (c)2019 http://dro1d.ru
 */

namespace Chat\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Chat settings';

    //protected $_help_url = 'zira/help/chat-settings';

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

        $configs = Zira\Config::getArray();

        $form = new \Chat\Forms\Settings();
        if (!array_key_exists('chat_trash_time', $configs)) $configs['chat_trash_time'] = floor(\Chat\Chat::TRASH_TIME / 86400);
        if (!array_key_exists('chat_captcha', $configs)) $configs['chat_captcha'] = 1;
        if (!array_key_exists('chat_captcha_users', $configs)) $configs['chat_captcha_users'] = 1;
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}