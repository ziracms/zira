<?php
/**
 * Zira project.
 * holidays.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Holidays extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-gift';
    protected static $_title = 'Holidays';

    //protected $_help_url = 'zira/help/holidays';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(\Holiday\Windows\Holiday::getClass());
        $this->setEditActionWindowClass(\Holiday\Windows\Holiday::getClass());

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Show banner','holiday'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_holidays_preview, this);', 'edit', true)
        );
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Show banner','holiday'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_holidays_preview, this);', 'edit', true)
        );
        
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Holidays settings', 'holiday'), 'glyphicon glyphicon-cog', 'desk_call(dash_holidays_settings, this);', 'settings', false, true)
        );
        
        $this->addDefaultOnLoadScript('desk_call(dash_holidays_load, this);');
        
        $this->addVariables(array(
            'dash_holiday_settings_wnd' => Dash\Dash::getInstance()->getWindowJSName(\Holiday\Windows\Settings::getClass())
        ));

        $this->includeJS('holiday/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $holidays = \Holiday\Models\Holiday::getCollection()->get();

        $items = array();
        foreach($holidays as $holiday) {
            $items[]=$this->createBodyFileItem($holiday->title, $holiday->description, $holiday->id, 'desk_window_edit_item(this);', false, array('type'=>'txt','inactive'=>$holiday->active ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}