<?php
/**
 * Zira project.
 * meta.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Meta extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-tag';
    protected static $_title = 'Website settings';

    protected $_help_url = 'zira/help/site-settings';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_meta_load, this);'
            )
        );

        $this->includeJS('dash/meta');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $configs = Zira\Config::getArray();

        $form = new \Dash\Forms\Meta();
        if (!array_key_exists('records_limit', $configs)) $configs['records_limit'] = 10;
        if (!array_key_exists('widget_records_limit', $configs)) $configs['widget_records_limit'] = 5;
        if (!array_key_exists('category_childs_list', $configs)) $configs['category_childs_list'] = true;
        if (!array_key_exists('comments_enabled', $configs)) $configs['comments_enabled'] = 1;
        if (!array_key_exists('site_favicon', $configs)) $configs['site_favicon'] = 'favicon.ico';
        if (!array_key_exists('site_scroll_effects', $configs)) $configs['site_scroll_effects'] = 1;
        if (!array_key_exists('site_parse_images', $configs)) $configs['site_parse_images'] = 1;
        $form->setValues($configs);

        $this->setBodyContent($form);
    }
}