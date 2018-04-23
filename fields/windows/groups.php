<?php
/**
 * Zira project.
 * groups.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Windows;

use Dash\Dash;
use Dash\Windows\Window;
use Zira;
use Zira\Permission;

class Groups extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-tags';
    protected static $_title = 'Extra field groups';

    //protected $_help_url = 'zira/help/extra-field-groups';
    
    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setSidebarEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(\Fields\Windows\Group::getClass());
        $this->setEditActionWindowClass(\Fields\Windows\Group::getClass());
        
        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Extra fields settings', 'fields'), 'glyphicon glyphicon-cog', 'desk_call(dash_fields_settings_wnd, this);', 'settings', false, true)
        );
        
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Group fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_group_fields, this);', 'edit', true, array('typo'=>'groupfields'))
        );
        
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Group fields', 'fields'), 'glyphicon glyphicon-tags', 'desk_call(dash_fields_group_fields, this);', 'edit', true, array('typo'=>'groupfields'))
        );
        
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_fields_group_drag, this);'
            )
        );

        $this->addDefaultOnLoadScript(
            'desk_call(dash_fields_group_load, this);'
        );

        $this->setData(array(
            'language' => ''
        ));

        $this->addVariables(array(
            'dash_fields_blank_src' => Zira\Helper::imgUrl('blank.png'),
            'dash_fields_fields_wnd' => Dash::getInstance()->getWindowJSName(\Fields\Windows\Fields::getClass()),
            'dash_fields_field_wnd' => Dash::getInstance()->getWindowJSName(\Fields\Windows\Field::getClass()),
            'dash_fields_values_wnd' => Dash::getInstance()->getWindowJSName(\Fields\Windows\Values::getClass()),
            'dash_fields_settings_wnd' => Dash::getInstance()->getWindowJSName(\Fields\Windows\Settings::getClass())
        ));

        $this->includeJS('fields/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setData(array(
                'language' => ''
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $language= (string)Zira\Request::post('language');
        if (!empty($language) && !in_array($language, Zira\Config::get('languages'))) {
            $language = '';
        }

        $query = \Fields\Models\Group::getCollection();
        if (!empty($language)) {
            $query->where('language', '=', $language);
        }
        $query->order_by('sort_order', 'asc');
        
        $rows = $query->get();

        $items = array();
        foreach($rows as $row) {
            $items[]=$this->createBodyItem(Zira\Helper::html($row->title), Zira\Helper::html($row->description), Zira\Helper::imgUrl('drag.png'), $row->id, 'desk_call(dash_fields_group_fields, this);', false, array('activated'=>$row->active,'sort_order'=>$row->sort_order));
        }

        $this->setBodyItems($items);

        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown())
        );

        if (count(Zira\Config::get('languages'))>1) {
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_fields_group_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
        }

        $this->setMenuItems($menu);

        $this->setData(array(
            'language' => $language
        ));
    }
}