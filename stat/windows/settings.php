<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cog';
    protected static $_title = 'Statistics settings';

    //protected $_help_url = 'zira/help/stat-settings';

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
        if (!array_key_exists('stat_exclude_bots', $configs)) $configs['stat_exclude_bots'] = 1;

        $form = new \Stat\Forms\Settings();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}