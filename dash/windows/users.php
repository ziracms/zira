<?php
/**
 * Zira project.
 * users.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Users extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-user';
    protected static $_title = 'Users';

    protected $_help_url = 'zira/help/users';

    public $search;
    public $page = 0;
    public $pages = 0;
    public $order = 'asc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);

        $this->pages = ceil($this->total/$this->limit);

        $this->setCreateActionWindowClass(User::getClass());
        $this->setEditActionWindowClass(User::getClass());

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Delete avatar'), 'glyphicon glyphicon-ban-circle', 'desk_call(dash_users_delete_image, this);', 'edit', true, array('typo'=>'noimage'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_users_deactivate, this);', 'delete', true, array('typo'=>'deactivate'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_users_activate, this);', 'delete', true, array('typo'=>'activate'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Delete avatar'), 'glyphicon glyphicon-ban-circle', 'desk_call(dash_users_delete_image, this);', 'edit', true, array('typo'=>'noimage'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_users_deactivate, this);', 'delete', true, array('typo'=>'deactivate'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_users_activate, this);', 'delete', true, array('typo'=>'activate'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Show avatar'), 'glyphicon glyphicon-picture', 'desk_call(dash_users_show_avatar, this);', 'edit', true, array('typo'=>'show_avatar'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('View profile'), 'glyphicon glyphicon-user', 'desk_call(dash_users_view_profile, this);', 'edit', true, array('typo'=>'view_profile'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Show avatar'), 'glyphicon glyphicon-picture', 'desk_call(dash_users_show_avatar, this);', 'edit', true, array('typo'=>'show_avatar'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('View profile'), 'glyphicon glyphicon-user', 'desk_call(dash_users_view_profile, this);', 'edit', true, array('typo'=>'view_profile'))
        );

        $groupsMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_users_groups, this);', 'groups'),
            $this->createMenuDropdownSeparator()
        );

        $groups = Zira\Models\Group::getArray();
        foreach($groups as $group_id=>$group_name) {
            $groupsMenu []= $this->createMenuDropdownItem($group_name, 'glyphicon glyphicon-filter', 'desk_call(dash_users_group_filter, this, element);', 'groups', false, array('group_id'=>$group_id));
        }

        $this->setMenuItems(array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Groups'), $groupsMenu)
        ));

        $this->setSidebarContent('<div class="user-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_users_select, this);'
            )
        );

        $this->addDefaultOnLoadScript(
                'desk_call(dash_users_load, this);'
        );

        $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'limit'=>$this->limit,
                'order'=>$this->order,
                'group_id'=>0
            ));

        $this->addStrings(array(
            'Information'
        ));

        $this->addVariables(array(
            'dash_user_status_active' => Zira\Models\User::STATUS_ACTIVE,
            'dash_user_status_not_active' => Zira\Models\User::STATUS_NOT_ACTIVE,
            'dash_user_status_verified' => Zira\Models\User::STATUS_VERIFIED,
            'dash_user_status_not_verified' => Zira\Models\User::STATUS_NOT_VERIFIED,
            'dash_user_profile_nophoto_src' => Zira\User::getProfileNoPhotoUrl()
        ), true);

        $this->addVariables(array(
            'dash_users_group_wnd' => Dash::getInstance()->getWindowJSName(Groups::getClass())
        ));

        $this->includeJS('dash/users');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CREATE_USERS) && !Permission::check(Permission::TO_EDIT_USERS)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'limit'=>$this->limit,
                'order'=>$this->order,
                'group_id'=>0
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $limit= (int)Zira\Request::post('limit');
        if ($limit > 0) {
            $this->limit = $limit < \Dash\Dash::MAX_LIMIT ? $limit : \Dash\Dash::MAX_LIMIT;
        }
        $group_id = intval(Zira\Request::post('group_id'));
        if (!empty($group_id)) {
            $group = new Zira\Models\Group($group_id);
            if ($group->loaded()) {
                $this->setTitle(Zira\Locale::t('Group') . ' - ' . Zira\Locale::t($group->name));
            } else {
                $group_id = 0;
            }
        }
        if (empty($group_id)) {
            $this->setTitle(Zira\Locale::t('Users'));
        }

        if (empty($this->search)) {
            if (empty($group_id)) {
                $this->total = Zira\Models\User::getAllUsersCount();
            } else {
                $this->total = Zira\Models\User::getGroupAllUsersCount($group_id);
            }
            $this->pages = ceil($this->total / $this->limit);
            if ($this->page > $this->pages) $this->page = $this->pages;
            if ($this->page < 1) $this->page = 1;
            if ($this->total>0) {
                if (empty($group_id)) {
                    $users = Zira\Models\User::getAllUsers($this->limit, ($this->page - 1) * $this->limit, $this->order);
                } else {
                    $users = Zira\Models\User::getGroupAllUsers($group_id, $this->limit, ($this->page - 1) * $this->limit, $this->order);
                }
            } else {
                $users = array();
            }
        } else {
            $this->total = Zira\Models\User::getSearchUsersCount($this->search, $group_id);
            $this->pages = ceil($this->total / $this->limit);
            if ($this->page > $this->pages) $this->page = $this->pages;
            if ($this->page < 1) $this->page = 1;
            if ($this->total>0) {
                $users = Zira\Models\User::searchUsers($this->search, $this->limit, ($this->page - 1) * $this->limit, $this->order, $group_id);
            } else {
                $users = array();
            }
        }

        $this->setData(array(
            'search'=>$this->search,
            'page'=>$this->page,
            'pages'=>$this->pages,
            'limit'=>$this->limit,
            'order'=>$this->order,
            'group_id'=>$group_id
        ));

        $items = array();
        foreach($users as $user) {
            $items[]=$this->createBodyItem(Zira\Helper::html($user->username), Zira\User::getProfileName($user), Zira\User::getProfilePhotoThumb($user, Zira\User::getProfileNoPhotoUrl()), $user->id, 'desk_call(dash_users_edit, this);', false, array('activated'=>$user->active,'avatar'=>Zira\User::getProfilePhoto($user)));
        }
        $this->setBodyItems($items);

        // menu
        $groupsMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('Edit'), 'glyphicon glyphicon-pencil', 'desk_call(dash_users_groups, this);', 'groups'),
            $this->createMenuDropdownSeparator()
        );

        $groups = Zira\Models\Group::getArray();
        foreach($groups as $_group_id=>$group_name) {
            if (empty($group_id) || $group_id!=$_group_id) $class = 'glyphicon-filter';
            else $class = 'glyphicon-ok';
            $groupsMenu []= $this->createMenuDropdownItem($group_name, 'glyphicon '.$class, 'desk_call(dash_users_group_filter, this, element);', 'groups', false, array('group_id'=>$_group_id));
        }

         $this->setMenuItems(array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Groups'), $groupsMenu)
        ));
    }
}