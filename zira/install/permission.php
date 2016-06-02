<?php
/**
 * Zira project.
 * permission.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Permission extends Table {
    protected $_table = 'permissions';

    public function __construct() {
        parent::__construct($this->_table);
    }

    public function getFields() {
        return array(
            'id' => Field::primary(),
            'group_id' => Field::int(true, true),
            'module' => Field::string(true),
            'name' => Field::string(true),
            'allow' => Field::tinyint(true, true, 0)
        );
    }

    public function getKeys() {
        return array(

        );
    }

    public function getUnique() {
        return array(
            'name_by_group' => array('group_id', 'name')
        );
    }

    protected function getDefaultSuperAdminPermissions() {
        return array(
            \Zira\Permission::TO_ACCESS_DASHBOARD => 1,
            \Zira\Permission::TO_EXECUTE_TASKS => 1,
            \Zira\Permission::TO_CHANGE_OPTIONS => 1,
            \Zira\Permission::TO_CHANGE_LAYOUT => 1,
            \Zira\Permission::TO_CREATE_USERS => 1,
            \Zira\Permission::TO_EDIT_USERS => 1,
            \Zira\Permission::TO_DELETE_USERS => 1,
            \Zira\Permission::TO_UPLOAD_FILES => 1,
            \Zira\Permission::TO_DELETE_FILES => 1,
            \Zira\Permission::TO_VIEW_FILES => 1,
            \Zira\Permission::TO_UPLOAD_IMAGES => 1,
            \Zira\Permission::TO_DELETE_IMAGES => 1,
            \Zira\Permission::TO_VIEW_IMAGES => 1,
            \Zira\Permission::TO_CREATE_RECORDS => 1,
            \Zira\Permission::TO_EDIT_RECORDS => 1,
            \Zira\Permission::TO_DELETE_RECORDS => 1,
            \Zira\Permission::TO_VIEW_RECORDS => 1,
            \Zira\Permission::TO_VIEW_RECORD => 1,
            \Zira\Permission::TO_MODERATE_COMMENTS => 1
        );
    }

    protected function getDefaultAdminPermissions() {
        return array(
            \Zira\Permission::TO_ACCESS_DASHBOARD => 1,
            \Zira\Permission::TO_EXECUTE_TASKS => 0,
            \Zira\Permission::TO_CHANGE_OPTIONS => 0,
            \Zira\Permission::TO_CHANGE_LAYOUT => 0,
            \Zira\Permission::TO_CREATE_USERS => 0,
            \Zira\Permission::TO_EDIT_USERS => 0,
            \Zira\Permission::TO_DELETE_USERS => 0,
            \Zira\Permission::TO_UPLOAD_FILES => 0,
            \Zira\Permission::TO_DELETE_FILES => 0,
            \Zira\Permission::TO_VIEW_FILES => 1,
            \Zira\Permission::TO_UPLOAD_IMAGES => 1,
            \Zira\Permission::TO_DELETE_IMAGES => 1,
            \Zira\Permission::TO_VIEW_IMAGES => 1,
            \Zira\Permission::TO_CREATE_RECORDS => 1,
            \Zira\Permission::TO_EDIT_RECORDS => 1,
            \Zira\Permission::TO_DELETE_RECORDS => 1,
            \Zira\Permission::TO_VIEW_RECORDS => 1,
            \Zira\Permission::TO_VIEW_RECORD => 1,
            \Zira\Permission::TO_MODERATE_COMMENTS => 1
        );
    }

    protected function getDefaultUserPermissions() {
        return array(
            \Zira\Permission::TO_ACCESS_DASHBOARD => 0,
            \Zira\Permission::TO_EXECUTE_TASKS => 0,
            \Zira\Permission::TO_CHANGE_OPTIONS => 0,
            \Zira\Permission::TO_CHANGE_LAYOUT => 0,
            \Zira\Permission::TO_CREATE_USERS => 0,
            \Zira\Permission::TO_EDIT_USERS => 0,
            \Zira\Permission::TO_DELETE_USERS => 0,
            \Zira\Permission::TO_UPLOAD_FILES => 0,
            \Zira\Permission::TO_DELETE_FILES => 0,
            \Zira\Permission::TO_VIEW_FILES => 0,
            \Zira\Permission::TO_UPLOAD_IMAGES => 0,
            \Zira\Permission::TO_DELETE_IMAGES => 0,
            \Zira\Permission::TO_VIEW_IMAGES => 0,
            \Zira\Permission::TO_CREATE_RECORDS => 0,
            \Zira\Permission::TO_EDIT_RECORDS => 0,
            \Zira\Permission::TO_DELETE_RECORDS => 0,
            \Zira\Permission::TO_VIEW_RECORDS => 1,
            \Zira\Permission::TO_VIEW_RECORD => 1,
            \Zira\Permission::TO_MODERATE_COMMENTS => 0
        );
    }

    public function getDefaults() {
        $permissionsArray = \Zira\Permission::getPermissionsArray();
        $groupsArray = \Zira\User::getDefaultGroupsArray();
        $inserts = array();
        foreach($groupsArray as $group_id) {
            switch($group_id) {
                case \Zira\User::GROUP_SUPERADMIN:
                    $permissions = self::getDefaultSuperAdminPermissions();
                    break;
                case \Zira\User::GROUP_ADMIN:
                    $permissions = self::getDefaultAdminPermissions();
                    break;
                default:
                    $permissions = self::getDefaultUserPermissions();
                    break;
            }
            foreach($permissionsArray as $name) {
                $inserts[]=array(
                    'id' => null,
                    'group_id' => $group_id,
                    'module' => 'zira',
                    'name' => $name,
                    'allow' => array_key_exists($name, $permissions) ? $permissions[$name] : 0
                );
            }
        }
        return $inserts;
    }
}