<?php
/**
 * Zira project.
 * options.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Options extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-wrench';
    protected static $_title = 'System settings';

    protected $_help_url = 'zira/help/options';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_options_load, this);'
            )
        );

        $this->includeJS('dash/options');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();
        if (!array_key_exists('dash_panel_frontend', $configs)) $configs['dash_panel_frontend'] = 1;
        if (empty($configs['timezone'])) $configs['timezone'] = date_default_timezone_get();
        if (!array_key_exists('db_widgets_enabled', $configs)) $configs['db_widgets_enabled'] = 1;

        $form = new \Dash\Forms\Options();
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}