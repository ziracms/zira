<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Forum settings';

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
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Forum\Forms\Settings();
        if (!array_key_exists('forum_limit', $configs)) $configs['forum_limit'] = 10;
        if (!array_key_exists('forum_min_chars', $configs)) $configs['forum_min_chars'] = 10;
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}