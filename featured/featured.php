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
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-star', Zira\Locale::tm('Featured records', 'featured', null, Dash\Dash::getDashLanguage()), null, 'featuredWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('featuredWindow', 'Featured\Windows\Featured', 'Featured\Models\Dash');
            Dash\Dash::unloadDashLanguage();
            Zira\Hook::register(Dash\Windows\Records::RECORDS_MENU_HOOK, array(get_class(), 'dashRecordsMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_CONTEXT_MENU_HOOK, array(get_class(), 'dashRecordsContextMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_ON_SELECT_CALLBACK_HOOK, array(get_class(), 'dashRecordsOnSelectCallbackHook'));
        }
    }

    public static function dashRecordsMenuHook($window) {
        return array(
            $window->createMenuDropdownItem(Zira\Locale::tm('Featured records', 'featured'), 'glyphicon glyphicon-star-empty', 'featuredWindow();', 'create'),
            $window->createMenuDropdownItem(Zira\Locale::tm('Add to featured', 'featured'), 'glyphicon glyphicon-star', 'desk_call(dash_featured_add, this);', 'edit', true, array('typo'=>'featured'))
        );
    }
    
    public static function dashRecordsContextMenuHook($window) {
        return $window->createContextMenuItem(Zira\Locale::tm('Add to featured', 'featured'), 'glyphicon glyphicon-star', 'desk_call(dash_featured_add, this);', 'edit', true, array('typo'=>'featured'));
    }
    
    public static function dashRecordsOnSelectCallbackHook() {
        return 'desk_call(dash_featured_on_record_select, this);';
    }
}