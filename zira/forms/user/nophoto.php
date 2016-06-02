<?php
/**
 * Zira project.
 * nophoto.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Nophoto extends Form {
    protected $_id = 'user-nophoto-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Remove photo'));
        $this->setDescription(Locale::t('Please enter your password'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->password(Locale::t('Current password').'*','password-current');
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
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