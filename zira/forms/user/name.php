<?php
/**
 * Zira project.
 * name.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Name extends Form {
    protected $_id = 'user-name-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Change username'));
        $this->setDescription(Locale::t('At least %s characters required',User::LOGIN_MIN_CHARS));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Username').'*','login');
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
        $validator->registerString('login',User::LOGIN_MIN_CHARS,User::LOGIN_MAX_CHARS,true,Locale::t('Invalid username'));
        $validator->registerRegexp('login', User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        if (!User::isUserPasswordChecked()) {
            $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
        }
        $validator->registerExists('login', \Zira\Models\User::getClass(), 'username', Locale::t('Specified login already exists'));
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