<?php
/**
 * Zira project.
 * login.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Controllers;

use Zira;
use Oauth;

class Login extends Zira\Controller {
    protected static $_error_status = Zira\Response::STATUS_403;
    
    public function _before() {
        parent::_before();
        if (Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/profile');
        }
    }

    public function facebook() {
        Oauth\Oauth::getInstance()->includeFacebookSdk();

        $enabled = Zira\Config::get('oauth_fb_on');
        $app_id = Zira\Config::get('oauth_fb_app_id');
        $app_secret = Zira\Config::get('oauth_fb_app_secret');

        if (!$enabled || !$app_id || !$app_secret) {
            Zira\Response::forbidden();
        }

        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.5',
        ]);

        // getting access token
        $jsHelper = $fb->getJavaScriptHelper();
        try {
            $accessToken = $jsHelper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            Zira\Response::exception($e);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            Zira\Response::exception($e);
        }

        $fb->setDefaultAccessToken($accessToken);

        // getting user data
        try {
            $response = $fb->get(Oauth\Models\Oauth::FACEBOOK_API_URL);
            $userNode = $response->getGraphUser();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            Zira\Response::exception($e);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            Zira\Response::exception($e);
        }

        $id = $userNode->getId();
        $name = $userNode->getName();
        $email = $userNode->getField('email');

        if (empty($id) || empty($name)) {
            Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
        }

        // checking if user is already registered
        $fb_user = Oauth\Models\Fbuser::getCollection()
                            ->where('fb_id','=',$id)
                            ->get(0);

        if (!$fb_user) {
            // registering new user if not exists
            if (!empty($email)) {
                $user = Oauth\Models\Oauth::getUserByEmail($email);
            }
            if (empty($user)) {
                // new user
                $username = 'fb_' . $id;
                $name_parts = explode(' ', $name);
                if (count($name_parts) > 1) {
                    $firstname = array_shift($name_parts);
                    $secondname = implode(' ', $name_parts);
                } else {
                    $firstname = $name;
                    $secondname = $name;
                }
                $user = Oauth\Models\Oauth::registerUser($username, $email ? $email : $id.'@facebook.com', $firstname, $secondname, !empty($email));
            }

            // saving facebook user
            $fb_user = new Oauth\Models\Fbuser();
            $fb_user->user_id = $user->id;
            $fb_user->fb_id = $id;
            $fb_user->email = $email ? $email : '';
            $fb_user->profile_name = $name;
            $fb_user->date_created = date('Y-m-d H:i:s');
            $fb_user->save();
        } else {
            // getting existing user
            $user = new Zira\Models\User($fb_user->user_id);
            if (!$user->loaded()) {
                Zira\Response::error(Zira\Locale::tm('Sorry, this user is disabled', 'oauth'), self::$_error_status);
            }
        }

        if (!Oauth\Models\Oauth::isUserActive($user, !empty($fb_user->email))) {
            Zira\Response::error(Zira\Locale::tm('Sorry, this user is disabled', 'oauth'), self::$_error_status);
        }

        // logging in and redirecting
        Zira\User::onUserLogin(false);

        $redirect = Zira\Request::get('redirect');
        if (!empty($redirect) && strpos($redirect,'//')===false && strpos($redirect, '.')===false) {
            if ($redirect=='dash') Zira\Helper::setAddingLanguageToUrl(false);
            Zira\Response::redirect($redirect);
        } else {
            Zira\Response::redirect('user/profile');
        }
    }

    public function vkresponse() {
        Zira\View::render(array(
            'code' => Zira\Request::get('code')
        ),'oauth/vk-response');
    }

    public function vkontakte() {
        $enabled = Zira\Config::get('oauth_vk_on');
        $app_id = Zira\Config::get('oauth_vk_app_id');
        $app_secret = Zira\Config::get('oauth_vk_app_secret');

        if (!$enabled || !$app_id || !$app_secret) {
            Zira\Response::forbidden();
        }

        $code = Zira\Request::get('code');
        if (empty($code)) {
            Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
        }

        // getting access token with user_id and email
        $response = @file_get_contents(Oauth\Oauth::getVkontakteAccessTokenUrl($code));
        if (empty($response)) {
            Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
        }
        $data = json_decode($response, true);
        if (empty($data['user_id'])) {
            Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
        }
        $id = $data['user_id'];
        $email = !empty($data['email']) ? $data['email'] : '';

        // checking if user is already registered
        $vk_user = Oauth\Models\Vkuser::getCollection()
                            ->where('vk_id','=',$id)
                            ->get(0);

        if (!$vk_user) {
            // getting profile name
            $response = @file_get_contents(Oauth\Oauth::getVkontakteUserApiUrl($data['access_token'], $data['user_id']));
            if (empty($response)) {
                Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
            }
            $info = json_decode($response, true);
            if (empty($info['response']) ||
                !is_array($info['response']) ||
                count($info['response'])==0 ||
                empty($info['response'][0]['first_name']) ||
                empty($info['response'][0]['last_name'])
            ) {
                Zira\Response::error(Zira\Locale::tm('You have to grant access to your profile', 'oauth'), self::$_error_status);
            }
            $firstname = $info['response'][0]['first_name'];
            $secondname = $info['response'][0]['last_name'];

            // registering new user if not exists
            if (!empty($email)) {
                $user = Oauth\Models\Oauth::getUserByEmail($email);
            }
            if (empty($user)) {
                // new user
                $username = 'vk_' . $id;
                $user = Oauth\Models\Oauth::registerUser($username, $email ? $email : $id.'@vk.com', $firstname, $secondname, !empty($email));
            }

            // saving vk user
            $vk_user = new Oauth\Models\Vkuser();
            $vk_user->user_id = $user->id;
            $vk_user->vk_id = $id;
            $vk_user->email = $email ? $email : '';
            $vk_user->profile_name = $firstname.' '.$secondname;
            $vk_user->date_created = date('Y-m-d H:i:s');
            $vk_user->save();
        } else {
            // getting existing user
            $user = new Zira\Models\User($vk_user->user_id);
            if (!$user->loaded()) {
                Zira\Response::error(Zira\Locale::tm('Sorry, this user is disabled', 'oauth'), self::$_error_status);
            }
        }

        if (!Oauth\Models\Oauth::isUserActive($user, !empty($vk_user->email))) {
            Zira\Response::error(Zira\Locale::tm('Sorry, this user is disabled', 'oauth'), self::$_error_status);
        }

        // logging in and redirecting
        Zira\User::onUserLogin(false);

        $redirect = Zira\Request::get('redirect');
        if (!empty($redirect) && strpos($redirect,'//')===false && strpos($redirect, '.')===false) {
            if ($redirect=='dash') Zira\Helper::setAddingLanguageToUrl(false);
            Zira\Response::redirect($redirect);
        } else {
            Zira\Response::redirect('user/profile');
        }
    }
}