<?php
/**
 * Zira project.
 * password.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Password extends Form {
    protected $_id = 'user-password-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Password recovery'));
        $this->setDescription(Locale::t('Verification code was sent to your Email address'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Username or Email').'*','login');
        $html .= $this->input(Locale::t('Verification code').'*','code');
        $html .= $this->captcha(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $login = (string)$this->getValue('login');
        $is_email = strpos($login, '@') !== false;

        $validator = $this->getValidator();
        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        if (!$is_email) {
            $validator->registerString('login',User::LOGIN_MIN_CHARS,User::LOGIN_MAX_CHARS,true,Locale::t('Invalid username'));
            $validator->registerRegexp('login', User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        } else {
            $validator->registerEmail('login',true,Locale::t('Invalid email'));
        }
        $validator->registerString('code', 0, 0, true, Locale::t('Incorrect verification code'));
        $validator->registerCustom(array(get_class(), 'isUserExists'), 'login', Locale::t('User not found'));
        $validator->registerCustom(array(get_class(), 'checkVerificationCode'), array('code','login'), Locale::t('Incorrect verification code'));
    }

    public static function isUserExists($login) {
        $user = \Zira\Models\User::findAuthUser($login);
        if (!$user) return false;
        if ($user->verified != \Zira\Models\User::STATUS_VERIFIED) return false;
        User::setCurrent($user);
        return true;
    }

    public static function checkVerificationCode($code, $login) {
        return User::isRecoveryCodeCorrect($login, $code);
    }
}