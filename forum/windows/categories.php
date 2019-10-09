<?php
/**
 * Zira project.
 * categories.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Forum;
use Zira\Permission;

class Categories extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-folder-close';
    protected static $_title = 'Forum categories';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);

        $this->setCreateActionWindowClass(Forum\Windows\Category::getClass());
        $this->setEditActionWindowClass(Forum\Windows\Category::getClass());

        $this->setDeleteActionEnabled(true);

        $this->setOnOpenJSCallback(
            $this->createJSCallback('desk_call(dash_forum_categories_drag, this);')
        );

        $this->includeJS('forum/dash');
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('New category'), Zira\Locale::t('New category'), 'glyphicon glyphicon-plus-sign', 'desk_call(desk_window_create_item, this, this);', 'create')
        );

        if (count(Zira\Config::get('languages'))>1) {
            $menu = array(
                $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown())
            );

            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_forum_categories_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);

            $this->setMenuItems($menu);
        }

        $this->setData(array(
            'language' => ''
        ));
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $categories_q = Forum\Models\Category::getCollection();

        $language = Zira\Request::post('language');
        if (!empty($language)) {
            $categories_q->where('language', '=', $language);
        }

        $categories = $categories_q->order_by('sort_order', 'asc')
                                    ->get();

        $items = array();
        foreach($categories as $category) {
            $items[]=$this->createBodyItem(Zira\Helper::html($category->title), Zira\Helper::html($category->description), Zira\Helper::imgUrl('drag.png'), $category->id, null, false, array('sort_order'=>$category->sort_order));
        }
        $this->setBodyItems($items);
    }
}