<?php

namespace Update\V1;

use Zira\Db\Alter;
use Zira\Db\Field;

class Permission extends Alter {
    protected $_table = 'permissions';

    public function __construct() {
        parent::__construct($this->_table);
    }
    
    public function getValues() {
        $superAdminPermissions = \Zira\Install\Permission::getDefaultSuperAdminPermissions();
        $adminPermissions = \Zira\Install\Permission::getDefaultAdminPermissions();
        $userPermissions = \Zira\Install\Permission::getDefaultUserPermissions();
        if (!isset($superAdminPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]) ||
            !isset($adminPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]) ||
            !isset($userPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]) ||
            !isset($superAdminPermissions[\Zira\Permission::TO_VIEW_GALLERY]) ||
            !isset($adminPermissions[\Zira\Permission::TO_VIEW_GALLERY]) ||
            !isset($userPermissions[\Zira\Permission::TO_VIEW_GALLERY]) ||
            !isset($superAdminPermissions[\Zira\Permission::TO_LISTEN_AUDIO]) ||
            !isset($adminPermissions[\Zira\Permission::TO_LISTEN_AUDIO]) ||
            !isset($userPermissions[\Zira\Permission::TO_LISTEN_AUDIO]) ||
            !isset($superAdminPermissions[\Zira\Permission::TO_VIEW_VIDEO]) ||
            !isset($adminPermissions[\Zira\Permission::TO_VIEW_VIDEO]) ||
            !isset($userPermissions[\Zira\Permission::TO_VIEW_VIDEO])
        ) return array();
        
        return array(
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_SUPERADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_DOWNLOAD_FILES,
                'allow' => $superAdminPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_ADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_DOWNLOAD_FILES,
                'allow' => $adminPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_USER,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_DOWNLOAD_FILES,
                'allow' => $userPermissions[\Zira\Permission::TO_DOWNLOAD_FILES]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_SUPERADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_GALLERY,
                'allow' => $superAdminPermissions[\Zira\Permission::TO_VIEW_GALLERY]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_ADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_GALLERY,
                'allow' => $adminPermissions[\Zira\Permission::TO_VIEW_GALLERY]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_USER,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_GALLERY,
                'allow' => $userPermissions[\Zira\Permission::TO_VIEW_GALLERY]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_SUPERADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_LISTEN_AUDIO,
                'allow' => $superAdminPermissions[\Zira\Permission::TO_LISTEN_AUDIO]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_ADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_LISTEN_AUDIO,
                'allow' => $adminPermissions[\Zira\Permission::TO_LISTEN_AUDIO]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_USER,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_LISTEN_AUDIO,
                'allow' => $userPermissions[\Zira\Permission::TO_LISTEN_AUDIO]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_SUPERADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_VIDEO,
                'allow' => $superAdminPermissions[\Zira\Permission::TO_VIEW_VIDEO]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_ADMIN,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_VIDEO,
                'allow' => $adminPermissions[\Zira\Permission::TO_VIEW_VIDEO]
            ),
            array(
                'id' => null,
                'group_id' => \Zira\User::GROUP_USER,
                'module' => 'zira',
                'name' => \Zira\Permission::TO_VIEW_VIDEO,
                'allow' => $userPermissions[\Zira\Permission::TO_VIEW_VIDEO]
            )
        );
    }
}
