<?php
/**
 * Zira project.
 * user.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class User extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_USERS) && !Permission::check(Permission::TO_EDIT_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\User();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ((empty($id) && !Permission::check(Permission::TO_CREATE_USERS)) ||
                (!empty($id) && !Permission::check(Permission::TO_EDIT_USERS))
            ) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            if (!empty($id)) {
                $user = new Zira\Models\User($id);
            } else {
                $user = new Zira\Models\User();
            }

            $user->group_id = (int)$form->getValue('group_id');
            $user->username = $form->getValue('username');
            $user->email = $form->getValue('email');
            $password = $form->getValue('password');
            if (empty($id) || !empty($password)) {
                $user->password = Zira\User::getHashedUserToken($password);
            }
            $user->firstname = $form->getValue('firstname');
            $user->secondname = $form->getValue('secondname');
            $user->country = $form->getValue('country');
            $user->city = $form->getValue('city');
            $user->address = $form->getValue('address');
            $user->phone = $form->getValue('phone');
            $dob = $form->getValue('dob');
            if (!empty($dob)) $user->dob = $form->parseDatepickerDate($dob);
            else $user->dob = null;
            $user->verified = intval($form->getValue('verified')) ? Zira\Models\User::STATUS_VERIFIED : Zira\Models\User::STATUS_NOT_VERIFIED;
            $user->active = intval($form->getValue('active')) ? Zira\Models\User::STATUS_ACTIVE : Zira\Models\User::STATUS_NOT_ACTIVE;
            if (empty($id)) {
                $user->date_created = date('Y-m-d H:i:s');
                $user->date_logged = date('Y-m-d H:i:s');
                $user->code = Zira\User::generateRememberCode($user->username, $user->email);
            }
            $image_path = $form->getValue('image');
            if (empty($image_path) && $user->image) {
                Zira\User::deletePhoto($user);
                $user->image = null;
            } else if (!empty($image_path) && strpos($image_path, UPLOADS_DIR.DIRECTORY_SEPARATOR)===0) {
                $imageArr = Zira\File::getFileArray($image_path);
                $image = Zira\User::savePhoto($user, $imageArr);
                if ($image) {
                    $user->image = $image;
                } else {
                    $image = null;
                }
            }

            $user->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}