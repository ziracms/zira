<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields;

use Zira;
use Dash;

class Fields {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-tags', Zira\Locale::tm('Extra fields', 'fields', null, Dash\Dash::getDashLanguage()), null, 'fieldsGroupsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsGroupsWindow', 'Fields\Windows\Groups', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsGroupWindow', 'Fields\Windows\Group', 'Fields\Models\Groups');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsItemsWindow', 'Fields\Windows\Fields', 'Fields\Models\Fields');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsItemWindow', 'Fields\Windows\Field', 'Fields\Models\Fields');
            Dash\Dash::getInstance()->registerModuleWindowClass('fieldsValuesWindow', 'Fields\Windows\Values', 'Fields\Models\Values');
            Dash\Dash::unloadDashLanguage();
            Zira\Hook::register(Dash\Windows\Records::RECORDS_MENU_HOOK, array(get_class(), 'dashRecordsMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_CONTEXT_MENU_HOOK, array(get_class(), 'dashRecordsContextMenuHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_SIDEBAR_HOOK, array(get_class(), 'dashRecordsSidebarHook'));
            Zira\Hook::register(Dash\Windows\Records::RECORDS_ON_SELECT_CALLBACK_HOOK, array(get_class(), 'dashRecordsOnSelectCallbackHook'));
        }
  
        Zira\View::registerRenderHook($this, 'beforeRender');
    }
    
    public static function dashRecordsMenuHook($window) {
        return array(
            $window->createMenuDropdownItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'))
        );
    }
    
    public static function dashRecordsContextMenuHook($window) {
        return $window->createContextMenuItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'));
    }
    
    public static function dashRecordsSidebarHook($window) {
        return $window->createSidebarItem(Zira\Locale::tm('Extra fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_records_open, this);', 'edit', true, array('typo'=>'fields'));
    }
    
    public static function dashRecordsOnSelectCallbackHook() {
        return 'desk_call(dash_fields_records_on_select, this);';
    }
    
    public static function beforeRender() {
        $record_id = Zira\Page::getRecordId();
        if ($record_id) {
            
        }
    }
}