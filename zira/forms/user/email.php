<?php
/**
 * Zira project.
 * email.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Email extends Form {
    protected $_id = 'user-email-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Change email'));
        $this->setDescription(Locale::t('Enter a valid email'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Email').'*','email');
        $html .= $this->checkbox(Locale::t('recieve notifications'), 'subscribed');
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
        $validator->registerEmail('email',true,Locale::t('Invalid email'));
        if (!User::isUserPasswordChecked()) {
            $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
        }
        //$validator->registerExists('email', \Zira\Models\User::getClass(), 'email', Locale::t('Specified email already exists'));
        $validator->registerCustom(array(get_class(), 'checkEmailExists'), 'email', Locale::t('Specified email already exists'));
    }

    public static function checkEmailExists($email) {
        $user = User::getCurrent();
        if (!$user || !User::isAuthorized()) return false;
        $exists = \Zira\Models\User::getCollection()
                    ->count()
                    ->where('email','=',$email)
                    ->and_where('id','<>',$user->id)
                    ->get('co');

        return !(bool)$exists;
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