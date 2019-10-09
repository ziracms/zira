<?php
/**
 * Zira project.
 * menu.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Menu extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-link';
    protected static $_title = 'Menu';

    public $item;

    protected $menu;
    protected $parent = 0;
    protected $language = '';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setBodyViewListVertical(true);

        $this->setEditActionWindowClass(Menuitem::getClass());

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New item'), 'glyphicon glyphicon-plus-sign', 'desk_window_create_item(this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New item'), 'glyphicon glyphicon-plus-sign', 'desk_window_create_item(this);', 'create')
        );

        $this->addDefaultOnLoadScript('desk_call(dash_menu_load, this);');

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_menu_select, this);'
            )
        );

        $this->setDeleteActionEnabled(true);
    }
    
    protected function _createSidebarItems() {
        $widgets = Zira\Models\Widget::getCollection()
                                ->where('name', '=', Zira\Menu::WIDGET_CLASS)
                                ->get();
        $items = array();
        $params = array();
        $items []= $this->createSidebarItem(Zira\Locale::t('Top menu'), 'glyphicon glyphicon-chevron-up', 'desk_call(dash_menu_top, this);', 'menu', false, array('typo'=>'topmenu', 'typoclass'=>'menu'));
        $items []= $this->createSidebarItem(Zira\Locale::t('Bottom menu'), 'glyphicon glyphicon-chevron-down', 'desk_call(dash_menu_bottom, this);', 'menu', false, array('typo'=>'bottommenu', 'typoclass'=>'menu'));
        foreach ($widgets as $widget) {
            if ($widget->params == Zira\Menu::MENU_PRIMARY ||
                $widget->params == Zira\Menu::MENU_SECONDARY || 
                $widget->params == Zira\Menu::MENU_FOOTER || 
                in_array($widget->params, $params)
            ) {
                continue;
            }
            $items []= $this->createSidebarItem(Zira\Locale::t('Menu').' #'.$widget->params, 'glyphicon glyphicon-chevron-right', 'desk_call(dash_menu_custom, this, '.$widget->params.');', 'menu', false, array('typo'=>'custommenu', 'typoclass'=>'menu', 'menu_id'=>$widget->params));
            $params []= $widget->params;
        }
        $items []= $this->createSidebarItem(Zira\Locale::t('New widget'), 'glyphicon glyphicon-plus', 'desk_call(dash_menu_new, this);', 'menu', false, array('typo'=>'newmenu', 'typoclass'=>'menu'));
        $items []= $this->createSidebarSeparator();
        $items []= $this->createSidebarItem(Zira\Locale::t('Secondary menu'), 'glyphicon glyphicon-expand', 'desk_call(dash_menu_secondary, this);', 'edit', false, array('typo'=>'secondary'));
        $items []= $this->createSidebarItem(Zira\Locale::t('Child items'), 'glyphicon glyphicon-collapse-down', 'desk_call(dash_menu_child, this);', 'edit', false, array('typo'=>'childitems'));
        $this->setSidebarItems($items);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('New item'), Zira\Locale::t('New menu item'), 'glyphicon glyphicon-plus-sign', 'desk_call(dash_menu_new_item, this);', 'create')
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Up'), 'glyphicon glyphicon-level-up', 'desk_call(dash_menu_up, this);', 'level', true)
        );

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_menu_new_item, this);'
            )
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Secondary menu'), 'glyphicon glyphicon-expand', 'desk_call(dash_menu_secondary, this);', 'edit', false, array('typo'=>'secondary'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Child items'), 'glyphicon glyphicon-collapse-down', 'desk_call(dash_menu_child, this);', 'edit', false, array('typo'=>'childitems'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_menu_view, this);', 'edit', true, array('typo'=>'preview'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_menu_page, this);', 'edit', true, array('typo'=>'newtab'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Delete menu'), 'glyphicon glyphicon-remove-circle', 'desk_call(dash_menu_delete, this);', 'create', true, array('typo'=>'menudelete'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Secondary menu'), 'glyphicon glyphicon-expand', 'desk_call(dash_menu_secondary, this);', 'edit', false, array('typo'=>'secondary'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Child items'), 'glyphicon glyphicon-collapse-down', 'desk_call(dash_menu_child, this);', 'edit', false, array('typo'=>'childitems'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_menu_view, this);', 'edit', true, array('typo'=>'preview'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_menu_page, this);', 'edit', true, array('typo'=>'newtab'))
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_menu_open, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_menu_drop, this, element);'
            )
        );

        $this->setData(array(
            'menu' => Zira\Menu::MENU_PRIMARY,
            'parent' => $this->parent,
            'language' => $this->language
        ));

        $this->addVariables(array(
            'dash_menu_primary_id' => Zira\Menu::MENU_PRIMARY,
            'dash_menu_secondary_id' => Zira\Menu::MENU_SECONDARY,
            'dash_menu_footer_id' => Zira\Menu::MENU_FOOTER,
            'dash_menu_new_id' => \Dash\Models\Menu::NEW_MENU_ID,
            'dash_menu_blank_src' => Zira\Helper::imgUrl('blank.png'),
            'dash_menu_menuitem_wnd' => Dash::getInstance()->getWindowJSName(Menuitem::getClass()),
            'dash_menu_web_wnd' => Dash::getInstance()->getWindowJSName(Web::getClass())
        ));

        $this->includeJS('dash/menu');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            $this->setData(array(
                'menu' => Zira\Menu::MENU_PRIMARY,
                'parent' => 0,
                'language' => ''
            ));
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $this->menu = (int)Zira\Request::post('menu');
        
        if ($this->menu == \Dash\Models\Menu::NEW_MENU_ID) {
            $this->menu = \Dash\Models\Menu::createNewMenu();
        }

        $parent = (int)Zira\Request::post('parent');
        if ($parent) $this->parent = $parent;

        $language = Zira\Request::post('language');
        if (!empty($language) && in_array($language, Zira\Config::get('languages'))) {
            $this->language = $language;
        }

        $query = Zira\Models\Menu::getCollection();
        if (empty($this->language)) {
            $query->where('menu_id', '=', $this->menu);
            if (!empty($this->parent)) {
                $query->and_where('parent_id', '=', $this->parent);
            } else {
                $query->and_where('parent_id', '=', 0);
            }
        } else {
            $query->open_query();
            $query->where('menu_id', '=', $this->menu);
            if (!empty($this->parent)) {
                $query->and_where('parent_id', '=', $this->parent);
            } else {
                $query->and_where('parent_id', '=', 0);
            }
            $query->and_where('language','is',null);
            $query->close_query();
            $query->union();
            $query->open_query();
            $query->where('menu_id', '=', $this->menu);
            if (!empty($this->parent)) {
                $query->and_where('parent_id', '=', $this->parent);
            } else {
                $query->and_where('parent_id', '=', 0);
            }
            $query->and_where('language','=',$this->language);
            $query->close_query();
            $query->merge();
        }
        $query->order_by('sort_order', 'asc');
        $rows = $query->get();

        $items = array();
        foreach($rows as $row) {
            $items[]=$this->createBodyItem($row->title, '['.$row->url.']', Zira\Helper::imgUrl('drag.png'), $row->id, null, false, array('parent'=>$row->parent_id,'sort_order'=>$row->sort_order,'url'=>Zira\Menu::parseURL($row->url,false),'inactive'=>$row->active==Zira\Models\Menu::STATUS_ACTIVE ? 0 : 1));
        }

        $this->setBodyItems($items);

        $icon = self::$_icon_class;
        $title = Zira\Locale::t(self::$_title);
        if ($this->menu == Zira\Menu::MENU_PRIMARY) {
            $title = Zira\Locale::t('Top menu');
        } else if ($this->menu == Zira\Menu::MENU_SECONDARY) {
            $title = Zira\Locale::t('Secondary menu');
        } else if ($this->menu == Zira\Menu::MENU_FOOTER) {
            $title = Zira\Locale::t('Bottom menu');
        }

        if ($this->parent) {
            $_parent = new Zira\Models\Menu($this->parent);
            if ($_parent->loaded()) {
                $title .= ' - '.$_parent->title;
                $icon = 'glyphicon glyphicon-collapse-down';
            }
        }
        if ($this->menu == Zira\Menu::MENU_SECONDARY) {
            $icon = 'glyphicon glyphicon-expand';
        }
        $this->setTitle($title);
        $this->setIconClass($icon);

        // menu
        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
        );

        if (count(Zira\Config::get('languages'))>1) {
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_menu_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
        }

        $this->setMenuItems($menu);
        
        //sidebar
        $this->_createSidebarItems();

        $this->setData(array(
            'menu' => $this->menu,
            'parent' => $this->parent,
            'language' => $this->language
        ));
    }
}