<?php
/**
 * Zira project.
 * permissions.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Permissions extends Model {
    public static function getFieldName($permission_name) {
        return mb_strtolower(str_replace(' ','_',$permission_name), CHARSET);
    }

    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Permissions();
        if ($form->isValid()) {
            $group_id = (int)$form->getValue('group_id');
            if ($group_id==Zira\User::GROUP_SUPERADMIN) return array('error' => Zira\Locale::t('Permission denied'));
            $permissions = Zira\Models\Permission::getCollection()
                        ->where('group_id','=',$group_id)
                        ->get(null, true)
                        ;
            foreach($permissions as $permission) {
                $permission['allow'] = (int)$form->getValue(self::getFieldName($permission['name']));
                $obj = new Zira\Models\Permission();
                $obj->loadFromArray($permission);
                $obj->save();
            }

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}