<?php
/**
 * Zira project.
 * users.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Users extends Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_DELETE_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $user_id) {
            $user = new Zira\Models\User($user_id);
            if (!$user->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            if ($user->id == Zira\User::getCurrent()->id) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $user->delete();
            Zira\User::deletePhoto($user);
        }

        return array('reload' => $this->getJSClassName());
    }

    public function deleteAvatar($user_id) {
        if (empty($user_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $user = new Zira\Models\User($user_id);
        if (!$user->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        if (!$user->image) return array('error' => Zira\Locale::t('An error occurred'));

        Zira\User::deletePhoto($user);
        $user->image = null;
        $user->save();

        return array('reload'=>$this->getJSClassName());
    }

    public function deactivate($users) {
        if (empty($users) || !is_array($users)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co=0;
        foreach($users as $user_id) {
            $user = new Zira\Models\User(intval($user_id));
            if (!$user->loaded()) continue;
            if ($user->active!=Zira\Models\User::STATUS_ACTIVE) continue;
            if ($user->id == Zira\User::getCurrent()->id) continue;
            $user->active=Zira\Models\User::STATUS_NOT_ACTIVE;
            $user->save();
            $co++;
        }
        return array('message' => Zira\Locale::t('Deactivated %s users', $co), 'reload'=>$this->getJSClassName());
    }

    public function activate($users) {
        if (empty($users) || !is_array($users)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co=0;
        foreach($users as $user_id) {
            $user = new Zira\Models\User(intval($user_id));
            if (!$user->loaded()) continue;
            if ($user->active!=Zira\Models\User::STATUS_NOT_ACTIVE) continue;
            if ($user->id == Zira\User::getCurrent()->id) continue;
            $user->active=Zira\Models\User::STATUS_ACTIVE;
            $user->save();
            $co++;
        }
        return array('message' => Zira\Locale::t('Activated %s users', $co), 'reload'=>$this->getJSClassName());
    }

    public function info($user_id) {
        if (empty($user_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_USERS) && !Permission::check(Permission::TO_EDIT_USERS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $user = Zira\Models\User::findUser($user_id);
        if (!$user) return array('error' => Zira\Locale::t('An error occurred'));

        $info = array();
        $class = $user->verified ? 'glyphicon glyphicon-ok-sign' : 'glyphicon glyphicon-minus-sign';
        $title = $user->verified ? Zira\Locale::t('Verified user') : Zira\Locale::t('Not verified user');
        $info[]='<span class="'.$class.'" title="'.$title.'"></span> '.Zira\Helper::html(Zira\User::getProfileName($user));
        $info[]='<span class="glyphicon glyphicon-user" title="'.Zira\Locale::t('Group').'"></span> '.Zira\Helper::html(Zira\Locale::t($user->group_name));
        $location = Zira\User::getProfileLocation($user,'');
        if (!empty($location)) {
            $info[] = '<span class="glyphicon glyphicon-map-marker" title="'.Zira\Locale::t('Location').'"></span> ' . Zira\Helper::html($location);
        }
        $dob = Zira\User::getProfileDob($user,'');
        if (!empty($dob)) {
            $info[] = '<span class="glyphicon glyphicon-gift" title="'.Zira\Locale::t('Date of birth').'"></span> ' . Zira\Helper::html($dob);
        }
        $info[]='<span class="glyphicon glyphicon-calendar" title="'.Zira\Locale::t('Sign-up date').'"></span> '.date(Zira\Config::get('date_format'), strtotime($user->date_created));
        $info[]='<span class="glyphicon glyphicon-log-in" title="'.Zira\Locale::t('Last login date').'"></span> '.date(Zira\Config::get('date_format'), strtotime($user->date_logged));
        $info[]='<span class="glyphicon glyphicon-comment" title="'.Zira\Locale::t('Comments').' / '.Zira\Locale::t('Posts').'"></span> '.Zira\Helper::html($user->comments) . ' / ' .Zira\Helper::html($user->posts);

        return $info;
    }
}