<?php
/**
 * Zira project.
 * groups.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Groups extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-user';
    protected static $_title = 'Groups';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setToolbarEnabled(false);
        $this->setSidebarEnabled(false);
        $this->setBodyViewListVertical(true);
    }

    public function create() {
        $this->setMenuItems(array(
            $this->createMenuItem(Zira\Locale::t('Actions'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Create'), 'glyphicon glyphicon-file', 'desk_call(dash_groups_create, this);', 'create'),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Permissions'), 'glyphicon glyphicon-flag', 'desk_call(dash_groups_permissions, this);', 'edit', true, array('typo'=>'permissions')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Rename'), 'glyphicon glyphicon-tag', 'desk_call(dash_groups_rename, this);', 'edit'),
                $this->createMenuDropdownItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_groups_deactivate, this);', 'delete', true, array('typo'=>'deactivate')),
                $this->createMenuDropdownItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_groups_activate, this);', 'delete', true, array('typo'=>'activate')),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Delete'), 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete', true, array('typo'=>'delete')),
            ))
        ));

        $this->setContextMenuItems(array(
            $this->createContextMenuItem(Zira\Locale::t('Create'), 'glyphicon glyphicon-file', 'desk_call(dash_groups_create, this);', 'create'),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Permissions'), 'glyphicon glyphicon-flag', 'desk_call(dash_groups_permissions, this);', 'edit', true, array('typo'=>'permissions')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Rename'), 'glyphicon glyphicon-tag', 'desk_call(dash_groups_rename, this);', 'edit'),
            $this->createContextMenuItem(Zira\Locale::t('Deactivate'), 'glyphicon glyphicon-minus-sign', 'desk_call(dash_groups_deactivate, this);', 'delete', true, array('typo'=>'deactivate')),
            $this->createContextMenuItem(Zira\Locale::t('Activate'), 'glyphicon glyphicon-ok-circle', 'desk_call(dash_groups_activate, this);', 'delete', true, array('typo'=>'activate')),
            $this->createContextMenuSeparator(),
            $this->createContextMenuItem(Zira\Locale::t('Delete'), 'glyphicon glyphicon-remove-circle', 'desk_window_delete_items(this);', 'delete', true, array('typo'=>'delete')),
        ));

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_groups_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_groups_load, this);');

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_groups_permissions, this);'
            )
        );

        $this->setOnDeleteItemsJSCallback(
            $this->createJSCallback(
                'desk_call(dash_groups_delete, this);'
            )
        );

        $this->setOnCloseJSCallback(
            $this->createJSCallback(
                'desk_call(dash_groups_close, this);'
            )
        );

        $this->addStrings(array(
            'Enter name',
            'Permission denied'
        ));

        $this->addVariables(array(
            'dash_groups_status_active' => Zira\Models\Group::STATUS_ACTIVE,
            'dash_groups_status_not_active' => Zira\Models\Group::STATUS_NOT_ACTIVE,
            'dash_groups_superadmin_id' => Zira\User::GROUP_SUPERADMIN,
            'dash_groups_admin_id' => Zira\User::GROUP_ADMIN,
            'dash_groups_user_id' => Zira\User::GROUP_USER,
            'dash_groups_users_wnd' => Dash::getInstance()->getWindowJSName(Users::getClass()),
            'dash_groups_permission_wnd' => Dash::getInstance()->getWindowJSName(Permissions::getClass())
        ));

        $this->includeJS('dash/groups');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $groups = Zira\Models\Group::getList();
        $items = array();
        foreach ($groups as $group) {
            $items[]=$this->createBodyItem(Zira\Locale::t($group->name), Zira\Locale::t($group->name), Zira\User::getProfileNoPhotoUrl(), $group->id, null, false, array('activated'=>$group->active));
        }

        $this->setBodyItems($items);
    }
}