<?php
/**
 * Zira project.
 * login.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\Request;
use Zira\User;
use Zira\View;

class Login extends Form {
    protected $_id = 'user-login-form';

    const HOOK_NAME = 'user_login_form_hook';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $action = 'user/login';
        $redirect = Request::get('redirect');
        if (isset($redirect) && strpos($redirect, '//')===false && strpos($redirect, '.')===false) $action .= '?redirect='.$redirect;
        $this->setUrl($action);
        $this->setTitle(Locale::t('Sign In'));
        $this->setDescription(Locale::t('Enter your username or email'));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ jQuery(\'#'.$this->getId().'\').find(\'input[type=text]\').focus(); });';
        $script .= Helper::tag_close('script');
        //View::addHTML($script, View::VAR_HEAD_BOTTOM);
        View::addBodyBottomScript($script);
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Username or Email').'*','login');
        $html .= $this->password(Locale::t('Password').'*','password');
        $html .= $this->checkbox(Locale::t('Remember me'), 'rememberme');
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));

        $extra_items = \Zira\Hook::run(self::HOOK_NAME);
        if (!empty($extra_items)) {
            $html .= Helper::tag_open('div',array('class'=>'user-login-form-extra-items'));
            foreach($extra_items as $item) {
                $html .= Helper::tag_open('div',array('class'=>'user-login-form-extra-item'));
                $html .= $item;
                $html .= Helper::tag_close('div');
            }
            $html .= Helper::tag_close('div');
        }

        $html .= Helper::tag_open('p',array('class'=>'text-right'));
        $html .= Helper::tag('a', Locale::t('Forgot password ?'), array('href'=>Helper::url('user/recover')));
        if (\Zira\Config::get('user_signup_allow')) {
            $html .= '&nbsp;|&nbsp;';
            $html .= Helper::tag('a', Locale::t('Sign Up'), array('href' => Helper::url('user/signup')));
        }
        $html .= Helper::tag_close('p');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $login = (string)$this->getValue('login');
        $is_email = strpos($login, '@') !== false;

        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        if (!$is_email) {
            $validator->registerString('login',User::LOGIN_MIN_CHARS,User::LOGIN_MAX_CHARS,true,Locale::t('Invalid username'));
            $validator->registerRegexp('login', User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        } else {
            $validator->registerEmail('login',true,Locale::t('Invalid email'));
        }
        $validator->registerString('password',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerRegexp('password', User::REGEXP_PASSWORD, Locale::t('Password contain bad characters'));
        $validator->registerCustom(array(get_class(), 'checkPassword'), array('login','password'), $is_email ? Locale::t('Email or password incorrect') : Locale::t('Username or password incorrect'));
    }

    public static function checkPassword($login, $password) {
        $success = User::isPasswordCorrect($login, $password);
        if ($success) {
            User::setUserPasswordChecked();
        }
        return $success;
    }
}