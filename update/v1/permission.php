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
            !isset($userPermissions[\Zira\Permission::TO_DOWNLOAD_FILES])
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
            )
        );
    }
}
