<?php
/**
 * Zira project.
 * categories.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Forum;
use Zira\Permission;

class Categories extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-folder-close';
    protected static $_title = 'Forum categories';

    protected $_help_url = 'zira/help/forum-categories';

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
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $categories = Forum\Models\Category::getCollection()
                                            ->order_by('sort_order', 'asc')
                                            ->get();

        $items = array();
        foreach($categories as $category) {
            $items[]=$this->createBodyItem($category->title, $category->description, Zira\Helper::imgUrl('drag.png'), $category->id, null, false, array('sort_order'=>$category->sort_order));
        }
        $this->setBodyItems($items);
    }
}