<?php
/**
 * Zira project.
 * Pwd.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Pwd extends Form {
    protected $_id = 'user-pwd-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Change password'));
        $this->setDescription(Locale::t('At least %s characters required',User::PASSWORD_MIN_CHARS));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->password(Locale::t('New password').'*','password');
        $html .= $this->password(Locale::t('Repeat password').'*','password-match');
        $html .= $this->password(Locale::t('Current password').'*','password-current');
        $html .= $this->captchaLazy(Locale::t('Anti-Bot').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('password',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerString('password-match',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerMatch('password','password-match',Locale::t('Passwords do not match'));
        $validator->registerRegexp('password', User::REGEXP_PASSWORD, Locale::t('Password contain bad characters'));
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