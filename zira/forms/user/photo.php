<?php
/**
 * Zira project.
 * photo.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Config;
use Zira\Form;
use Zira\Locale;
use Zira\User;

class Photo extends Form {
    protected $_id = 'user-photo-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setMultipart(true);
        $this->setTitle(Locale::t('Change photo'));
        $this->setDescription(Locale::t('Allowed file extensions: %s', 'jpg, png, gif'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->fileButton(Locale::t('Photo').'*','photo');
        if (!User::isUserPasswordChecked()) {
            $html .= $this->password(Locale::t('Current password').'*','password-current');
        }
        $html .= $this->captchaLazy(Locale::t('Anti-Bot').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerImage('photo', 0, true, Locale::t('Invalid image file'));
        $validator->registerCustom(array(get_class(), 'checkSize'), 'photo', Locale::t('Photo size should be at least %s pixels',Config::get('user_photo_min_width').'x'.config::get('user_photo_min_height')));
        if (!User::isUserPasswordChecked()) {
            $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
        }
    }

    public static function checkSize($photo) {
        $size = @getimagesize($photo['tmp_name']);
        if ($size[0]<Config::get('user_photo_min_width')) return false;
        if ($size[1]<Config::get('user_photo_min_height')) return false;
        return true;
    }

    public static function checkPassword($password) {
        $user = User::getCurrent();
        if (!$user || !User::isAuthorized()) return false;
        $success = User::isPasswordCorrect($user->username, $password);
        if ($success) {
            User::setUserPasswordChecked();
        }
        return $success;
    }
}