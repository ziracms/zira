<?php
/**
 * Zira project.
 * groups.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Groups extends Model {
    public function rename($group_id, $name) {
        $name = trim($name);
        if (empty($name)) return array('error' => Zira\Locale::t('An error occurred'));
        if (empty($group_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $group = new Zira\Models\Group($group_id);
        if (!$group->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $group->name = Zira\Helper::html($name);
        $group->save();

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function deactivate($groups) {
        if (empty($groups) || !is_array($groups)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co=0;
        foreach($groups as $group_id) {
            $group = new Zira\Models\Group(intval($group_id));
            if (!$group->loaded()) continue;
            if ($group->active!=Zira\Models\Group::STATUS_ACTIVE) continue;
            if ($group->id == Zira\User::getCurrent()->group_id) continue;
            $group->active=Zira\Models\Group::STATUS_NOT_ACTIVE;
            $group->save();
            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Deactivated %s groups', $co), 'reload'=>$this->getJSClassName());
    }

    public function activate($groups) {
        if (empty($groups) || !is_array($groups)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co=0;
        foreach($groups as $group_id) {
            $group = new Zira\Models\Group(intval($group_id));
            if (!$group->loaded()) continue;
            if ($group->active!=Zira\Models\Group::STATUS_NOT_ACTIVE) continue;
            if ($group->id == Zira\User::getCurrent()->group_id) continue;
            $group->active=Zira\Models\Group::STATUS_ACTIVE;
            $group->save();
            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s groups', $co), 'reload'=>$this->getJSClassName());
    }

    public function createGroup($name) {
        $name = trim($name);
        if (empty($name) || Zira\Helper::utf8BadMatch($name)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $group = new Zira\Models\Group();
        $group->name = Zira\Helper::html($name);
        $group->active = Zira\Models\Group::STATUS_ACTIVE;
        $group->save();

        $permissions = Zira\Models\Permission::getCollection()
                        ->select('name', 'allow')
                        ->where('group_id','=',Zira\User::GROUP_USER)
                        ->get()
                        ;

        foreach ($permissions as $permission) {
            $permissionObj = new Zira\Models\Permission();
            $permissionObj->name = $permission->name;
            $permissionObj->allow = $permission->allow;
            $permissionObj->group_id = $group->id;
            $permissionObj->module = Zira\Models\Permission::CUSTOM_PERMISSIONS_GROUP;
            $permissionObj->save();
        }

        Zira\Cache::clear();

        return array('reload'=>$this->getJSClassName());
    }

    public function deleteGroups($groups) {
        if (empty($groups) || !is_array($groups)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) || !Permission::check(Permission::TO_EDIT_USERS) || !Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co=0;
        foreach($groups as $group_id) {
            $group = new Zira\Models\Group(intval($group_id));
            if (!$group->loaded()) continue;
            if ($group->id == Zira\User::getCurrent()->group_id) continue;
            if (in_array($group->id, array(Zira\User::GROUP_SUPERADMIN, Zira\User::GROUP_ADMIN, Zira\User::GROUP_USER))) continue;
            $group->delete();

            Zira\Models\Permission::getCollection()
                        ->delete()
                        ->where('group_id','=',$group->id)
                        ->execute()
                        ;

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Deleted %s groups', $co), 'reload'=>$this->getJSClassName());
    }
}