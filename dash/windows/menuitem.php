<?php
/**
 * Zira project.
 * menuitem.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Menuitem extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-link';
    protected static $_title = 'Menu item';

    protected $_help_url = 'zira/help/menu-item';

    public $item;

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
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $menu = (int)Zira\Request::post('menu');
        $parent = (int)Zira\Request::post('parent');

        $form = new \Dash\Forms\Menuitem();

        if (!empty($this->item)) {
            $menuItem = new Zira\Models\Menu($this->item);
            if (!$menuItem->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

            $form->setValues($menuItem->toArray());
        } else {
            $form->setValues(array(
                'language' => '',
                'menu_id' => $menu,
                'parent_id' => $parent,
                'active' => Zira\Models\Menu::STATUS_ACTIVE
            ));
        }

        $this->setBodyContent($form);

        if (empty($this->item)) {
            $this->setTitle(Zira\Locale::t('New menu item'));
        } else {
            $this->setTitle(Zira\Locale::t('Menu item').' - '.$menuItem->title);
        }
    }
}