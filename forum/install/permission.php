<?php
/**
 * Zira project.
 * permission.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Install;

use Zira\Db\Table;
use Zira\Db\Field;

class Permission extends \Zira\Install\Permission {
    public function getFields() {
        return array();
    }

    public function getKeys() {
        return array();
    }

    public function getUnique() {
        return array();
    }

    public function getDefaults() {
        return array();
    }

    public function __toString() {
        return '';
    }

    public function install() {
        // adding custom permission to \Zira\Install\Permission table
        $groups = \Zira\Models\Group::getCollection()->get();
        foreach($groups as $group) {
            $allow = $group->id == \Zira\User::GROUP_SUPERADMIN || $group->id == \Zira\User::GROUP_ADMIN ? 1 : 0;
            $permissionObj = new \Zira\Models\Permission();
            $permissionObj->name = \Forum\Forum::PERMISSION_MODERATE;
            $permissionObj->allow = $allow;
            $permissionObj->group_id = $group->id;
            $permissionObj->module = 'forum';
            $permissionObj->save();
        }
    }

    public function uninstall() {
        // removing custom permission from \Zira\Install\Permission table
        \Zira\Models\Permission::getCollection()
                    ->delete()
                    ->where('module','=','forum')
                    ->execute();
    }

    public function dump($delimiter, $limit=1000, $flush = false) {
        return '';
    }
}