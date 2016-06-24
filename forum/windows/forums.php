<?php
/**
 * Zira project.
 * forums.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Forum;
use Zira\Permission;

class Forums extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Forums';


    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionText(Zira\Locale::tm('New forum', 'forum'));

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback('desk_call(dash_forum_forum_create, this);')
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback('desk_call(dash_forum_forum_edit, this);')
        );

        $this->setOnSelectJSCallback(
            $this->createJSCallback('desk_call(dash_forum_forums_select, this);')
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Categories'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_forum_categories, this)', 'create')
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Threads'), 'glyphicon glyphicon-comment', 'desk_call(dash_forum_threads, this)', 'edit')
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Forum threads', 'forum'), 'glyphicon glyphicon-comment', 'desk_call(dash_forum_threads, this);', 'edit', true, array('typo'=>'threads'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Open forum page', 'forum'), 'glyphicon glyphicon-new-window', 'desk_call(dash_forum_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Forum threads', 'forum'), 'glyphicon glyphicon-comment', 'desk_call(dash_forum_threads, this);', 'edit', true, array('typo'=>'threads'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Open forum page', 'forum'), 'glyphicon glyphicon-new-window', 'desk_call(dash_forum_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $category_menu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_forum_categories, this);', 'create')
        );

        $this->setMenuItems(array(
            $this->createMenuItem($this->_default_menu_title, $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Categories'), $category_menu)
        ));

        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Forum settings', 'forum'), 'glyphicon glyphicon-cog', 'desk_call(dash_forum_settings, this);', 'settings', false, true)
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback('desk_call(dash_forum_forums_drag, this);')
        );

        $this->addDefaultOnLoadScript('desk_call(dash_forum_forums_load, this);');

        $this->addVariables(array(
            'dash_forum_route' => Forum\Forum::ROUTE,
            'dash_forum_categories_wnd' => Dash\Dash::getInstance()->getWindowJSName(Categories::getClass()),
            'dash_forum_forum_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Forum::getClass()),
            'dash_forum_threads_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Topics::getClass()),
            'dash_forum_thread_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Topic::getClass()),
            'dash_forum_messages_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Messages::getClass()),
            'dash_forum_message_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Message::getClass()),
            'dash_forum_settings_wnd' => Dash\Dash::getInstance()->getWindowJSName(Forum\Windows\Settings::getClass())
        ));

        $this->includeJS('forum/dash');

        $this->setData(array(
            'category_id' => 0
        ));
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category_id = 0;
        $category_title = '';
        $_category_id = (int)Zira\Request::post('category_id');

        $category_menu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_forum_categories, this);', 'create'),
            $this->createMenuDropdownSeparator()
        );

        $categories = Forum\Models\Category::getCategpries();
        foreach($categories as $category) {
            if (!$_category_id) $_category_id = $category->id;
            if (!$category_id && $_category_id == $category->id) {
                $category_id = $category->id;
                $category_title = $category->title;
            }

            if (empty($category_id) || $category_id!=$category->id) $class = 'glyphicon-filter';
            else $class = 'glyphicon-ok';
            $category_menu []= $this->createMenuDropdownItem($category->title, 'glyphicon '.$class, 'desk_call(dash_forum_category_filter, this, '.$category->id.');', 'categories', false, array('category_id'=>$category->id));
        }

        $this->setSidebarItems($this->getDefaultSidebar());

        $forums = Forum\Models\Forum::getCollection()
                                    ->where('category_id','=',$category_id)
                                    ->order_by('sort_order', 'asc')
                                    ->get();

        $items = array();
        foreach($forums as $forum) {
            $items[]=$this->createBodyItem($forum->title, $forum->description, Zira\Helper::imgUrl('drag.png'), $forum->id, null, false, array('sort_order'=>$forum->sort_order,'inactive'=>$forum->active ? 0 : 1, 'page'=>Forum\Models\Forum::generateUrl($forum)));
        }
        $this->setBodyItems($items);

        $this->setMenuItems(array(
            $this->createMenuItem($this->_default_menu_title, $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Categories'), $category_menu)
        ));

        if (!empty($category_title)) {
            $this->setTitle(Zira\Locale::t(self::$_title).' - '.$category_title);
        }

        $this->setData(array(
            'category_id' => $category_id
        ));
    }
}