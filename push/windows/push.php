<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Push extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-cloud-upload';
    protected static $_title = 'Push notifications';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(false);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);
        
        $this->setDeleteActionEnabled(false);
    }

    public function create() {
//        $this->addDefaultMenuDropdownItem(
//            $this->createMenuDropdownSeparator()
//        );
//        $this->addDefaultMenuDropdownItem(
//            $this->createMenuDropdownItem(Zira\Locale::tm('Show banner','holiday'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_holidays_preview, this);', 'edit', true)
//        );
        
//        $this->addDefaultContextMenuItem(
//            $this->createContextMenuSeparator()
//        );
//        $this->addDefaultContextMenuItem(
//            $this->createContextMenuItem(Zira\Locale::tm('Show banner','holiday'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_holidays_preview, this);', 'edit', true)
//        );
        
//        $this->addDefaultToolbarItem(
//            $this->createToolbarButton(null, Zira\Locale::tm('Holidays settings', 'holiday'), 'glyphicon glyphicon-cog', 'desk_call(dash_holidays_settings, this);', 'settings', false, true)
//        );
        
//        $this->addDefaultOnLoadScript('desk_call(dash_holidays_load, this);');
        
//        $this->addVariables(array(
//            'dash_holiday_settings_wnd' => Dash\Dash::getInstance()->getWindowJSName(\Holiday\Windows\Settings::getClass())
//        ));

        $this->includeJS('push/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        
//        $this->setBodyItems($items);
    }
}