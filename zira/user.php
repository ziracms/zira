<?php
/**
 * Zira project.
 * user.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

use Dash\Dash;

class User {
    const LOGIN_MIN_CHARS = 4;
    const LOGIN_MAX_CHARS = 255;
    const PASSWORD_MIN_CHARS = 6;
    const PASSWORD_MAX_CHARS = 255;

    const REGEXP_LOGIN = '/^[a-z0-9_]+$/i';
    const REGEXP_PASSWORD = '/^[^\r\n\t\x{10000}-\x{10FFFF}]+$/u';

    const GROUP_SUPERADMIN = 1;
    const GROUP_ADMIN = 2;
    const GROUP_USER = 3;

    const CONFIG_ALLOW_SIGNUP = 'user_signup_allow';
    const CONFIG_ALLOW_VIEW_PROFILE = 'user_profile_view_allow';
    const CONFIG_ALLOW_LOGIN_CHANGE = 'user_login_change_allow';
    const CONFIG_VERIFY_EMAIL = 'user_email_verify';
    const CONFIG_CHECK_UA = 'user_check_ua';
    const CONFIG_CONFIRMATION_MESSAGE = 'user_email_confirmation_message';
    const CONFIG_RECOVERY_MESSAGE = 'user_password_recovery_message';
    const CONFIG_PASSWORD_MESSAGE = 'user_new_password_message';

    const COOKIE_ANONYMOUS_USER = 'zira_id';
    const COOKIE_REMEMBER_ME = 'zira_usr';
    const SESSION_AUTHORIZED_USER_ID = 'auth_user_id';
    const SESSION_CONFIRM_EMAIL = 'user_confirm_email';
    const SESSION_UA = 'user_ua';
    const SESSION_PASSWORD_CHECKED = 'user_password_checked';
    const SESSION_TOKEN = 'user_token';

    const PHOTO_EXT = 'jpg';
    const USER_NOPHOTO = 'nophoto.jpg';

    const PROFILE_LINKS_HOOK = 'zira_profile_links';
    const PROFILE_INFO_HOOK = 'zira_profile_info';

    const REMEMBER_ME_LIFETIME = 1209600; // two weeks
    const REMEMBER_ANONYMOUS_ID = 31536000; // one year

    protected static $_current;

    public static function setCurrent($user, $force = false) {
        if (self::isAuthorized() && !$force) return;
        self::$_current = $user;
    }

    public static function getCurrent() {
        return self::$_current;
    }

    public static function isSelf($user) {
        if (!$user->id) return false;
        if (!self::isAuthorized()) return false;
        return (self::$_current->id == $user->id);
    }

    public static function generatePasswordHash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function generateEmailConfirmationCode() {
        return Zira::randomSecureString(16);
    }

    public static function getHashedConfirmationCode($code) {
        return self::generatePasswordHash($code);
    }

    public static function verifyConfirmationCode($code, $hash) {
        return self::verifyPassword($code, $hash);
    }

    public static function generatePasswordRecoveryCode() {
        return Zira::randomSecureString(24);
    }

    public static function getHashedPasswordRecoveryCode($code) {
        return self::generatePasswordHash($code);
    }

    public static function verifyPasswordRecoveryCode($code, $hash) {
        return self::verifyPassword($code, $hash);
    }

    public static function generateRememberCode($login,$email) {
        do {
            $random = Zira::randomSecureString(16);
            $code = md5($random.$login.$email);
            $co = Models\User::getCollection()
                            ->count()
                            ->where('code','=',$code)
                            ->get('co');
        } while($co>0);

        return $code;
    }

    public static function generateUserToken() {
        return Zira::randomSecureString(32);
    }

    public static function getHashedUserToken($code) {
        return self::generatePasswordHash($code);
    }

    public static function verifyUserToken($token, $hash) {
        return password_verify($token, $hash);
    }

    public static function isVerificationCodeCorrect($login_or_email, $code) {
        if (empty($code)) return false;
        $user = Models\User::findAuthUser($login_or_email);
        if (!$user) return false;
        $hash = $user->vcode;
        if (empty($hash)) return false;
        if ($user->verified == \Zira\Models\User::STATUS_VERIFIED) return false;
        if (self::verifyConfirmationCode($code, $hash)) {
            self::setCurrent($user);
            return true;
        } else {
            return false;
        }
    }

    public static function isRecoveryCodeCorrect($login_or_email, $code) {
        if (empty($code)) return false;
        $user = Models\User::findAuthUser($login_or_email);
        if (!$user) return false;
        $hash = $user->vcode;
        if (empty($hash)) return false;
        if ($user->verified != \Zira\Models\User::STATUS_VERIFIED) return false;
        if (self::verifyPasswordRecoveryCode($code, $hash)) {
            self::setCurrent($user);
            return true;
        } else {
            return false;
        }
    }

    public static function getDefaultConfirmMessage() {
        $message = Locale::t('Hello %s !', '$user')."\r\n\r\n";
        $message .= Locale::t('Please confirm your Email address.')."\r\n";
        $message .= Locale::t('Your verification code: %s', '$code')."\r\n";
        $message .= Locale::t('Enter code on the following page %s','$url')."\r\n\r\n";
        $message .= Locale::t('You recieved this message, because your Email address was specified during registration process on %s','$site');
        return $message;
    }

    public static function sendConfirmEmail($email, $login, $code) {
        $message = Config::get(self::CONFIG_CONFIRMATION_MESSAGE);
        if (!$message || strlen(trim($message))==0) {
            $message = self::getDefaultConfirmMessage();
        } else {
            $message = Locale::t($message);
        }
        $message = str_replace('$user', $login, $message);
        $message = str_replace('$code', $code, $message);
        $message = str_replace('$url', Helper::url('user/confirm', true, true), $message);
        $message = str_replace('$site', Helper::url('/',true, true), $message);

        Mail::send($email, Locale::t('Email confirmation'), Helper::html($message));
    }

    public static function getDefaultRecoveryMessage() {
        $message = Locale::t('Hello %s !', '$user')."\r\n\r\n";
        $message .= Locale::t('We recieved password recovery request for your account.')."\r\n";
        $message .= Locale::t('Your verification code: %s', '$code')."\r\n";
        $message .= Locale::t('Enter code on the following page %s','$url')."\r\n\r\n";
        $message .= Locale::t('You recieved this message, because your Email address or login was specified during password recovery process on %s. If it was not you, ignore this message','$site');
        return $message;
    }

    public static function sendRecoverEmail($email, $login, $code) {
        $message = Config::get(self::CONFIG_RECOVERY_MESSAGE);
        if (!$message || strlen(trim($message))==0) {
            $message = self::getDefaultRecoveryMessage();
        } else {
            $message = Locale::t($message);
        }
        $message = str_replace('$user', $login, $message);
        $message = str_replace('$code', $code, $message);
        $message = str_replace('$url', Helper::url('user/password', true, true), $message);
        $message = str_replace('$site', Helper::url('/',true, true), $message);

        Mail::send($email, Locale::t('Password recovery'), Helper::html($message));
    }

    public static function getDefaultPasswordMessage() {
        $message = Locale::t('Hello %s !', '$user')."\r\n\r\n";
        $message .= Locale::t('Your new password: %s', '$code')."\r\n";
        $message .= Locale::t('You can now sign in on the following page %s','$url')."\r\n\r\n";
        $message .= Locale::t('You recieved this message, because your Email address or login was specified during password recovery process on %s','$site');
        return $message;
    }

    public static function sendPasswordEmail($email, $login, $password) {
        $message = Config::get(self::CONFIG_PASSWORD_MESSAGE);
        if (!$message || strlen(trim($message))==0) {
            $message = self::getDefaultPasswordMessage();
        } else {
            $message = Locale::t($message);
        }
        $message = str_replace('$user', $login, $message);
        $message = str_replace('$code', $password, $message);
        $message = str_replace('$url', Helper::url('user/login', true, true), $message);
        $message = str_replace('$site', Helper::url('/',true, true), $message);

        Mail::send($email, Locale::t('Your new password'), Helper::html($message));
    }

    public static function isPasswordCorrect($login, $password) {
        if (empty($login) || empty($password)) return false;
        $user = Models\User::findAuthUser($login);
        if (!$user) return false;
        if (!$user->username || !$user->password) return false;
        if (self::verifyPassword($password, $user->password)) {
            self::setCurrent($user);
            return true;
        } else {
            return false;
        }
    }

    public static function isAllowedToLogin() {
        $user = self::getCurrent();
        if (!$user) return false;
        if (!Config::get(self::CONFIG_VERIFY_EMAIL, true)) return true;
        return $user->verified == Models\User::STATUS_VERIFIED;
    }

    public static function rememberConfirmEmail($email) {
        Session::set(self::SESSION_CONFIRM_EMAIL, $email);
    }

    public static function getRememberedConfirmEmail() {
        return Session::get(self::SESSION_CONFIRM_EMAIL);
    }

    public static function rememberAuthorizedUserId($user_id) {
        Session::set(self::SESSION_AUTHORIZED_USER_ID, $user_id);
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        Session::set(self::SESSION_UA, $ua);
    }

    public static function forgetAuthorizedUserId() {
        Session::remove(self::SESSION_AUTHORIZED_USER_ID);
        Session::remove(self::SESSION_UA);
        self::unsetUserPasswordChecked();
    }

    public static function load() {
        User::initAnonymousUser();
        $user_id = intval(Session::get(self::SESSION_AUTHORIZED_USER_ID));
        if (!$user_id) {
            $user_id = self::loadRememberedUser();
            if (!$user_id) return;
        } else {
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            if (Config::get(self::CONFIG_CHECK_UA, true) && Session::get(self::SESSION_UA) != $ua) {
                self::forgetAuthorizedUserId();
                return;
            }
        }
        $user = self::getCurrent();
        if (!$user || !($user instanceof Models\User)) {
            $user = new Models\User($user_id);
            if (!$user->loaded()) $user = null;
        }
        if (!$user || !$user->active) {
            self::forgetAuthorizedUserId();
            self::forgetUser();
            if ($user) {
                $user->token = '';
                $user->save();
            }
            return;
        }
        self::setCurrent($user);
    }

    public static function isAuthorized() {
        $user = self::getCurrent();
        if (!$user) return false;
        if (!($user instanceof Models\User)) return false;
        return true;
    }

    public static function isVerified() {
        if (!self::isAuthorized()) return false;
        return self::$_current->verified == Models\User::STATUS_VERIFIED;
    }

    public static function onUserLogin($remember = false) {
        Session::regenerate();
        $user = self::getCurrent();
        if (!$user) return;
        if (!($user instanceof Models\User)) {
            $user = new Models\User($user->id);
            if (!$user) return;
        }
        $user->date_logged = date('Y-m-d H:i:s');
        if ($remember) {
            $token = self::generateUserToken();
            $ua=isset($_SERVER['HTTP_USER_AGENT']) && Config::get(self::CONFIG_CHECK_UA, true) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $user->token = self::getHashedUserToken($token.$ua);
            self::rememberUser($user->code, $token);
        } else {
            $user->token = '';
        }
        $user->save();
        self::setCurrent($user);
        self::rememberAuthorizedUserId($user->id);
    }

    public static function onUserLogout() {
        self::clearToken();
        Dash::clearToken();
        Session::regenerate();
    }

    public static function rememberUser($code, $token) {
        Cookie::set(self::COOKIE_REMEMBER_ME, $code.';'.$token, self::REMEMBER_ME_LIFETIME);
    }

    public static function forgetUser() {
        Cookie::remove(self::COOKIE_REMEMBER_ME);
    }

    public static function loadRememberedUser() {
        $cookie = Cookie::get(self::COOKIE_REMEMBER_ME);
        if (empty($cookie)) return null;
        $data = explode(';',$cookie);
        if (count($data)!=2 || empty($data[0]) || empty($data[1])) {
            self::forgetUser();
            return null;
        }
        $user = Models\User::getCollection()
                        ->select(Models\User::getFields())
                        ->join(Models\Group::getClass(), array('group_name'=>'name'))
                        ->where('code','=',$data[0])
                        ->and_where('active','=',Models\User::STATUS_ACTIVE)
                        ->and_where('active','=',Models\Group::STATUS_ACTIVE, Models\Group::getAlias())
                        ->get(0);
        if (!$user || !$user->token) {
            self::forgetUser();
            return null;
        }
        if (Config::get(self::CONFIG_VERIFY_EMAIL, true) && $user->verified != Models\User::STATUS_VERIFIED) {
            self::forgetUser();
            return null;
        }
        $ua=isset($_SERVER['HTTP_USER_AGENT']) && Config::get(self::CONFIG_CHECK_UA, true) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (!self::verifyUserToken($data[1].$ua, $user->token)) {
            $user = new Models\User($user->id);
            if ($user) {
                $user->token = '';
                $user->save();
            }
            self::forgetUser();
            return null;
        }
        $user = new Models\User($user->id);
        if (!$user) {
            return null;
        } else {
            self::setCurrent($user);
            self::onUserLogin(true);
            return $user->id;
        }
    }

    public static function setUserPasswordChecked() {
        Session::set(self::SESSION_PASSWORD_CHECKED, 1);
    }

    public static function unsetUserPasswordChecked() {
        Session::remove(self::SESSION_PASSWORD_CHECKED);
    }

    public static function isUserPasswordChecked() {
        return (bool)Session::get(self::SESSION_PASSWORD_CHECKED);
    }

    public static function generateUserPhotoName($user_id) {
        $random = Zira::randomSecureString(16);
        return md5($random.$user_id);
    }

    public static function generateAnonymousId() {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return self::getToken().md5($ip.microtime().$ua);
    }

    public static function getAnonymousUserId() {
        $anonymous_id = Cookie::get(self::COOKIE_ANONYMOUS_USER);
        if (empty($anonymous_id)) {
            $anonymous_id = self::generateAnonymousId();
        }
        return $anonymous_id;
    }

    public static function initAnonymousUser() {
        $anonymous_id = self::getAnonymousUserId();
        Cookie::set(self::COOKIE_ANONYMOUS_USER, $anonymous_id, self::REMEMBER_ANONYMOUS_ID);
    }

    protected static function generateToken() {
        return Zira::randomSecureString(8);
    }

    public static function getToken() {
        $exist = Session::get(self::SESSION_TOKEN);
        if ($exist) return $exist;

        $token = self::generateToken();
        Session::set(self::SESSION_TOKEN,$token);

        return $token;
    }

    public static function checkToken($token) {
        if (!$token) return false;
        $exist = Session::get(self::SESSION_TOKEN);
        if (!$exist) return false;

        return ($token == $exist);
    }

    public static  function clearToken() {
        Session::remove(self::SESSION_TOKEN);
    }

    public static function getUserPhotoFilename($image) {
        return $image.'.'.self::PHOTO_EXT;
    }

    public static function getUserThumbFilename($image) {
        return $image.'.thumb.'.self::PHOTO_EXT;
    }

    public static function deletePhoto($user) {
        if (!$user->image) return;
        $name = self::getUserPhotoFilename($user->image);
        $thumb_name = self::getUserThumbFilename($user->image);

        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . USERS_DIR . DIRECTORY_SEPARATOR;
        if (file_exists($dir.$name)) unlink($dir.$name);
        if (file_exists($dir.$thumb_name)) unlink($dir.$thumb_name);
    }

    public static function savePhoto($user, array $file) {
        if ($user->image) self::deletePhoto($user);
        $save_path = USERS_DIR;
        $max_width = Config::get('user_photo_max_width');
        $max_height = Config::get('user_photo_max_height');
        $thumb_width = Config::get('user_thumb_width');
        $thumb_height = Config::get('user_thumb_height');
        do {
            $image = self::generateUserPhotoName($user->id);
            $name = self::getUserPhotoFilename($image);
            $thumb_name = self::getUserThumbFilename($image);
        } while(file_exists(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $name) || file_exists(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name));
        $files = File::save($file, $save_path);
        if (!$files) return false;
        foreach($files as $path=>$_name) {
            $size = @getimagesize($path);
            if ($size[0]>$size[1]) {
                if ($size[0]<$max_width) $max_width = $size[0];
                if (!Image::resize($path, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $name, $max_width, null, self::PHOTO_EXT)) return false;
            } else if ($size[0]<=$size[1]) {
                if ($size[1]<$max_height) $max_height = $size[1];
                if (!Image::resize($path, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $name, null, $max_height, self::PHOTO_EXT)) return false;
            }
            if (!Image::createThumb($path, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name, $thumb_width, $thumb_height, self::PHOTO_EXT)) return false;
            unlink($path);
        }
        return $image;
    }

    public static function saveAvatar($user, $width_percent, $height_percent, $left_percent, $top_percent) {
        if (!$user->image) return false;
        $thumb_width = Config::get('user_thumb_width');
        $thumb_height = Config::get('user_thumb_height');
        $name = self::getUserPhotoFilename($user->image);
        $thumb_name = self::getUserThumbFilename($user->image);
        $save_path = USERS_DIR;
        if (!Image::crop(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $name, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name, $width_percent, $height_percent, $left_percent, $top_percent, self::PHOTO_EXT)) return false;
        if (!Image::resize(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name, $thumb_width, $thumb_height, self::PHOTO_EXT)) return false;
        do {
            $image = self::generateUserPhotoName($user->id);
            $new_name = self::getUserPhotoFilename($image);
            $new_thumb_name = self::getUserThumbFilename($image);
        } while(file_exists(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $new_name) || file_exists(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $new_thumb_name));
        if (
            !rename(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $thumb_name, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $new_thumb_name) ||
            !rename(File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $name, File::getAbsolutePath($save_path). DIRECTORY_SEPARATOR . $new_name)
        ) return false;
        return $image;
    }

    public static function isProfileVerified($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return false;
        return $user->verified == Models\User::STATUS_VERIFIED;
    }

    public static function getProfileName($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $name = trim($user->firstname.' '.$user->secondname);
        if (!empty($name)) return $name;
        return $user->username;
    }

    public static function getProfileLocation($user=null, $default = null) {
        if ($default === null) $default = Locale::t('not specified');
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $country = trim($user->country);
        $city = trim($user->city);
        $address = trim($user->address);
        $loc = array();
        if (!empty($country)) $loc []= $country;
        if (!empty($city)) $loc []= $city;
        if (!empty($address)) $loc []= $address;
        $loc = implode(', ',$loc);
        if (empty($loc)) $loc = $default;
        return $loc;
    }

    public static function getProfileDob($user=null, $default = null) {
        if ($default === null) $default = Locale::t('not specified');
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $dob = $user->dob;
        if (!empty($dob)) return date(Config::get('date_format'), strtotime($dob));
        return $default;
    }

    public static function getProfileSignupDate($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        return date(Config::get('date_format'), strtotime($user->date_created));
    }

    public static function getProfileLoginDate($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        return date(Config::get('date_format'), strtotime($user->date_logged));
    }

    public static function getProfileGroup($user=null, $default = null) {
        if ($default === null) $default = Locale::t('unknown');
        if($user===null) $user = self::getCurrent();
        if (!$user) return $default;
        $groups = Models\Group::getArray();
        if (!array_key_exists($user->group_id, $groups)) return $default;
        return $groups[$user->group_id];
    }

    public static function getProfileNoPhotoUrl() {
        return Helper::imgUrl(self::USER_NOPHOTO);
    }

    public static function getProfilePhoto($user=null, $default = '') {
        if($user===null) $user = self::getCurrent();
        if (!$user || !$user->image) return $default;

        $name = self::getUserPhotoFilename($user->image);

        return Helper::baseUrl(UPLOADS_DIR . '/' . USERS_DIR . '/' . $name);
    }

    public static function getProfilePhotoThumb($user=null, $default = '') {
        if($user===null) $user = self::getCurrent();
        if (!$user || !$user->image) return $default;

        $name = self::getUserThumbFilename($user->image);

        return Helper::baseUrl(UPLOADS_DIR . '/' . USERS_DIR . '/' . $name);
    }

    public static function getProfileEmail($user=null, $default = '') {
        if($user===null) $user = self::getCurrent();
        if (!$user) return $default;
        if (!self::isSelf($user)) return $default;
        return $user->email;
    }

    public static function getProfilePhone($user=null, $default = '') {
        if($user===null) $user = self::getCurrent();
        if (!$user || !$user->phone) return $default;
        if (!self::isSelf($user)) return $default;
        return $user->phone;
    }

    public static function getProfileUrlPath($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        return 'user/'.$user->id;
    }

    public static function getProfileUrl($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        return Helper::url('user/'.$user->id);
    }

    public static function getProfileComments($user=null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        return (int)$user->comments;
    }

    public static function getDefaultGroupsArray() {
        return array(
            self::GROUP_SUPERADMIN,
            self::GROUP_ADMIN,
            self::GROUP_USER
        );
    }

    public static function generateUserProfileLink($id, $firstname, $secondname, $username, $rel = null, $icon_class = '') {
        $name = $firstname && $secondname ? trim($firstname . ' ' . $secondname) : $username;
        $attr = array('href'=>Helper::url('user/'.$id));
        if ($rel !== null) $attr['rel'] = $rel;
        if (empty($icon_class)) return Helper::tag('a', $name, $attr);
        else {
            $html = Helper::tag_open('a', $attr);
            $html .= Helper::tag('span', null, array('class'=>$icon_class)).' ';
            $html .= Helper::html($name);
            $html .= Helper::tag_close('a');
            return $html;
        }
    }

    public static function generateUserProfileThumb($image, $default = null, array $attributes = array()) {
        $url = Helper::imgUrl('nophoto.jpg');
        if (!$image && $default!==null) $url = $default;
        else if ($image) {
            $name = self::getUserThumbFilename($image);
            $url = Helper::baseUrl(UPLOADS_DIR . '/' . USERS_DIR . '/' . $name);
        }
        $attributes['src'] = $url;
        $attributes['width'] = Config::get('user_thumb_width');
        $attributes['height'] = Config::get('user_thumb_height');
        return Helper::tag_short('img', $attributes);
    }

    public static function generateUserProfileThumbLink($id, $firstname, $secondname, $username, $rel = null, $image, $default_image = null, array $attributes = array()) {
        $name = $firstname && $secondname ? trim($firstname . ' ' . $secondname) : $username;
        $attr = array('href'=>Helper::url('user/'.$id),'title'=>$name);
        if ($rel !== null) $attr['rel'] = $rel;
        $html = Helper::tag_open('a', $attr);
        $html .= self::generateUserProfileThumb($image, $default_image, $attributes);
        $html .= Helper::tag_close('a');
        return $html;
    }

    public static function getProfileEditLinks() {
        $user = self::getCurrent();
        if (!$user) return array();
        $links = array();
        $links []= array(
            'url' => 'user/messages',
            'icon' => 'glyphicon glyphicon-envelope',
            'title' => Locale::t('Messages')
        );
        $links []= array(
            'type' => 'separator'
        );
        $links []= array(
            'url' => 'user/photo',
            'icon' => 'glyphicon glyphicon-picture',
            'title' => Locale::t('Edit photo')
        );
        if ($user->image) {
            $links [] = array(
                'url' => 'user/avatar',
                'icon' => 'glyphicon glyphicon-scissors',
                'title' => Locale::t('Edit avatar')
            );
            $links []= array(
            'url' => 'user/nophoto',
            'icon' => 'glyphicon glyphicon-ban-circle',
            'title' => Locale::t('Delete photo')
        );
        }
        $links []= array(
            'type' => 'separator'
        );
        $links []= array(
            'url' => 'user/edit',
            'icon' => 'glyphicon glyphicon-list-alt',
            'title' => Locale::t('Edit profile')
        );
        $links []= array(
            'url' => 'user/email',
            'icon' => 'glyphicon glyphicon-envelope',
            'title' => Locale::t('Edit email')
        );
        if (Config::get(self::CONFIG_ALLOW_LOGIN_CHANGE, true)) {
            $links [] = array(
                'url' => 'user/name',
                'icon' => 'glyphicon glyphicon-user',
                'title' => Locale::t('Edit username')
            );
        }
        $links []= array(
            'url' => 'user/pwd',
            'icon' => 'glyphicon glyphicon-lock',
            'title' => Locale::t('Edit password')
        );
        $extra_links = Hook::run(self::PROFILE_LINKS_HOOK);
        if (!empty($extra_links)) {
            $links = array_merge($links, $extra_links);
        }
        return $links;
    }

    public static function getProfileExtraInfo() {
        return Hook::run(self::PROFILE_INFO_HOOK);
    }

    public static function increaseMessagesCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->messages++;
        $user->save();
    }

    public static function decreaseMessagesCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->messages--;
        if ($user->messages<0) $user->messages=0;
        $user->save();
    }

    public static function increaseCommentsCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->comments++;
        $user->save();
    }

    public static function decreaseCommentsCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->comments--;
        if ($user->comments<0) $user->comments=0;
        $user->save();
    }

    public static function increasePostsCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->posts++;
        $user->save();
    }

    public static function decreasePostsCount($user = null) {
        if($user===null) $user = self::getCurrent();
        if (!$user) return '';
        $user->posts--;
        if ($user->posts<0) $user->posts=0;
        $user->save();
    }

    public static function isUserBlocked($user_id, $return_found_row = false) {
        if (!self::isAuthorized()) return false;

        $row=Models\Blacklist::getCollection()
                        ->where('user_id','=',self::getCurrent()->id)
                        ->and_where('blocked_user_id','=',$user_id)
                        ->get(0);

        if (!$row) return false;
        if (!$return_found_row) return true;
        else return $row;
    }

    public static function isCurrentBlocked($user_id, $return_found_row = false) {
        if (!self::isAuthorized()) return false;

        $row=Models\Blacklist::getCollection()
                        ->where('user_id','=',$user_id)
                        ->and_where('blocked_user_id','=',self::getCurrent()->id)
                        ->get(0);

        if (!$row) return false;
        if (!$return_found_row) return true;
        else return $row;
    }
}