<?php
/**
 * Zira project.
 * register.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Register extends Form {
    protected $_id = 'user-register-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('User Signup'));
        $this->setDescription(Locale::t('Pick a username and password'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('First name').'*','firstname',array('title'=>Locale::t('Please enter your first name')));
        $html .= $this->input(Locale::t('Second name').'*','secondname',array('title'=>Locale::t('Please enter your second name')));
        $html .= $this->input(Locale::t('Username').'*','username',array('title'=>Locale::t('At least %s characters required',User::LOGIN_MIN_CHARS)));
        $html .= $this->input(Locale::t('Email').'*','email',array('title'=>Locale::t('Enter a valid email')));
        $html .= $this->password(Locale::t('Password').'*','password',array('title'=>Locale::t('At least %s characters required',User::PASSWORD_MIN_CHARS)));
        $html .= $this->password(Locale::t('Repeat password').'*','password-match',array('title'=>Locale::t('Enter password again')));
        $html .= $this->captcha(Locale::t('Anti-Bot').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('firstname',0,0,true,Locale::t('Please enter your first name'));
        $validator->registerString('secondname',0,0,true,Locale::t('Please enter your second name'));
        $validator->registerNoTags('firstname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('firstname', Locale::t('Invalid character specified'));
        $validator->registerNoTags('secondname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('secondname', Locale::t('Invalid character specified'));
        $validator->registerString('username',User::LOGIN_MIN_CHARS,User::LOGIN_MAX_CHARS,true,Locale::t('Invalid username'));
        $validator->registerEmail('email',true,Locale::t('Invalid email'));
        $validator->registerString('password',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerString('password-match',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerRegexp('username', User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        $validator->registerMatch('password','password-match',Locale::t('Passwords do not match'));
        $validator->registerRegexp('password', User::REGEXP_PASSWORD, Locale::t('Password contain bad characters'));
        $validator->registerExists('username', \Zira\Models\User::getClass(), 'username', Locale::t('Specified login already exists'));
        $validator->registerExists('email', \Zira\Models\User::getClass(), 'email', Locale::t('Specified email already exists'));

        $firstname = trim((string)$this->getValue('firstname'));
        $secondname = trim((string)$this->getValue('secondname'));
        $this->updateValues(array(
            'firstname' => $firstname,
            'secondname' => $secondname
        ));
    }
}