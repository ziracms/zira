<?php
/**
 * Zira project.
 * confirm.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\User;

class Confirm extends Form {
    protected $_id = 'user-confirm-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Email confirmation'));
        $this->setDescription(Locale::t('Verification code was sent to your Email address'));
    }

    protected function _render() {
        $html = $this->open();
        if (!User::isAuthorized()) {
            $html .= $this->input(Locale::t('Username or Email').'*','login');
        }
        $html .= $this->input(Locale::t('Verification code').'*','code');
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= Helper::tag_open('p',array('class'=>'text-right'));
        $html .= Helper::tag('a', Locale::t('Did not recieve verification code ?'), array('href'=>Helper::url('user/send')));
        $html .= Helper::tag_close('p');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('code', 0, 0, true, Locale::t('Incorrect verification code'));
        if (!User::isAuthorized()) {
            $login = (string)$this->getValue('login');
            $is_email = strpos($login, '@') !== false;

            if (!$is_email) {
                $validator->registerString('login',User::LOGIN_MIN_CHARS,User::LOGIN_MAX_CHARS,true,Locale::t('Invalid username'));
                $validator->registerRegexp('login', User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
            } else {
                $validator->registerEmail('login',true,Locale::t('Invalid email'));
            }
            $validator->registerCustom(array(get_class(), 'checkVerificationCode'), array('code','login'), Locale::t('Incorrect verification code'));
        } else {
            $validator->registerCustom(array(get_class(), 'checkVerificationCode'), array('code'), Locale::t('Incorrect verification code'));
        }
    }

    public static function checkVerificationCode($code, $login=null) {
        if (User::isAuthorized()) {
            $current = User::getCurrent();
            $login = $current->username;
        }
        return User::isVerificationCodeCorrect($login, $code);
    }
}