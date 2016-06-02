<?php
/**
 * Zira project.
 * home.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Home extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-home';
    protected static $_title = 'Home page settings';

    protected $_help_url = 'zira/help/home-settings';

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
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Dash\Forms\Home();
        if (!array_key_exists('home_layout', $configs)) $configs['home_layout'] = Zira\Config::get('layout');
        if (!array_key_exists('home_records_limit', $configs)) $configs['home_records_limit'] = Zira\Config::get('records_limit', 10);
        if (!array_key_exists('home_records_enabled', $configs)) $configs['home_records_enabled'] = true;
        if (!array_key_exists('home_categories_order', $configs)) $configs['home_categories_order'] = 'asc';
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}