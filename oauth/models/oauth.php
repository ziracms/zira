<?php
/**
 * Zira project.
 * oauth.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Models;

use Zira;
use Zira\Models\User;

class Oauth {
    const FACEBOOK_JS_SDK = '//connect.facebook.net/en_US/sdk.js';
    const FACEBOOK_JS_SDK_RU = '//connect.facebook.net/ru_RU/sdk.js';
    const FACEBOOK_SDK_FOLDER = 'facebook-sdk-v5';
    const FACEBOOK_API_URL = '/me?fields=id,name,email';

    const VKONTAKTE_AUTH_URL = 'https://oauth.vk.com/authorize';
    const VKONTAKTE_ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';
    const VKONTAKTE_USER_API_URL = 'https://api.vk.com/method/users.get';

    public static function getUserByEmail($email) {
        return User::getCollection()
                ->where('email','=',$email)
                ->get(0);
    }

    public static function isUserActive($user, $trust_email) {
        $_user = User::findAuthUser($user->email);
        if (!$_user) return false;
        if ($trust_email && $_user->verified != Zira\Models\User::STATUS_VERIFIED) {
            User::getCollection()
                ->update(array(
                    'verified' => Zira\Models\User::STATUS_VERIFIED,
                    'password' => Zira\User::getHashedUserToken(Zira\User::generateUserToken())
                ))
                ->where('id','=',$user->id)
                ->execute();
        }
        Zira\User::setCurrent($_user);
        return true;
    }

    public static function registerUser($username, $email, $firstname, $secondname, $trust_email) {
        $user = new Zira\Models\User();
        $user->firstname = $firstname;
        $user->secondname = $secondname;
        $user->email = $email;
        $user->username = $username;
        $password = Zira\User::generateUserToken();
        $user->password = Zira\User::getHashedUserToken($password);
        $user->group_id = Zira\User::GROUP_USER;
        $user->date_created = date('Y-m-d H:i:s');
        $user->date_logged = date('Y-m-d H:i:s');
        $user->verified = $trust_email ? Zira\Models\User::STATUS_VERIFIED : Zira\Models\User::STATUS_NOT_VERIFIED;
        $user->active = Zira\Models\User::STATUS_ACTIVE;
        $user->code = Zira\User::generateRememberCode($user->username, $user->email);
        $user->save();

        if ($trust_email) {
            try {
                self::sendInformEmail($email, $firstname . ' ' . $secondname, $password);
            } catch(\Exception $e) {
                // ignore
            }
        }

        Zira\User::setCurrent($user);
        return $user;
    }

    public static function sendInformEmail($email, $username, $password) {
        $message = Zira\Locale::t('Hello %s !', '$user')."\r\n\r\n";
        $message .= Zira\Locale::t('We created an account for you on %s.','$site')."\r\n";
        $message .= Zira\Locale::t('Your new password: %s', '$password')."\r\n\r\n";
        $message .= Zira\Locale::t('You recieved this message, because you logged in to %s first time, using your social network account.','$site');

        $message = str_replace('$user', $username, $message);
        $message = str_replace('$password', $password, $message);
        $message = str_replace('$site', Zira\Helper::url('/',true, true), $message);

        Zira\Mail::send($email, Zira\Locale::t('Your new account'), Zira\Helper::html($message));
    }
}