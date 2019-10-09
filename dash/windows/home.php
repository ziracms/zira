<?php
/**
 * Zira project.
 * home.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Home extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-home';
    protected static $_title = 'Home page settings';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Categories'), Zira\Locale::t('Category sorting'), 'glyphicon glyphicon-sort', 'desk_call(dash_home_categories_wnd, this);', 'create', false, true)
        );

        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );

        $this->addVariables(array(
            'dash_home_categories_wnd' => \Dash\Dash::getInstance()->getWindowJSName(Homecategories::getClass())
        ));
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
        if (!array_key_exists('home_records_sorting', $configs)) $configs['home_records_sorting'] = Zira\Config::get('records_sorting', 'id');
        if (!array_key_exists('home_site_records_grid', $configs)) $configs['home_site_records_grid'] = Zira\Config::get('site_records_grid', 1);
        if (!array_key_exists('home_slider_type', $configs)) $configs['home_slider_type'] = Zira\Config::get('slider_type', 'default');
        if (!array_key_exists('home_slider_mode', $configs)) $configs['home_slider_mode'] = Zira\Config::get('slider_mode', 3);
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}