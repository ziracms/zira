<?php
/**
 * Zira project.
 * featured.php
 * (c)2016 http://dro1d.ru
 */

namespace Featured;

use Zira;
use Dash;

class Featured {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        Zira\Router::addRoute('featured', 'featured/index/index');
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-star', Zira\Locale::tm('Featured records', 'featured'), null, 'featuredWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('featuredWindow', 'Featured\Windows\Featured', 'Featured\Models\Dash');

            Zira\Hook::register(Dash\Windows\Records::RECORDS_MENU_HOOK, array(get_class(), 'dashRecordsMenuHook'));
        }
    }

    public static function dashRecordsMenuHook($window) {
        return array(
            $window->createMenuDropdownItem(Zira\Locale::tm('Add to featured', 'featured'), 'glyphicon glyphicon-star', 'desk_call(dash_featured_add, this);', 'edit'),
        );
    }
}