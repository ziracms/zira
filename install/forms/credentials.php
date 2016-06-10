<?php
/**
 * Zira project.
 * credentials.php
 * (c)2016 http://dro1d.ru
 */

namespace Install\Forms;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;

class Credentials extends Form {
    protected $_id = 'install-credentials-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Required information'));
        $this->setDescription(Locale::t('Please fill out form fields'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Website name').'*', 'site_name');
        $html .= $this->input(Locale::t('Website slogan').'*', 'site_slogan');
        $html .= $this->input(Locale::t('Contact Email').'*', 'email_from');
        $html .= $this->input(Locale::t('Secret key').'*', 'secret', array('placeholder'=>Locale::t('min. %s chars',8),'title'=>Locale::t('enter random chars')));
        $html .= Helper::tag('div', null, array('style'=>'margin: 40px 0px'));
        $html .= Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Helper::tag('label', Locale::t('Administrator').':', array('class'=>'col-sm-3 control-label'));
        $html .= Helper::tag_close('div');
        $html .= $this->input(Locale::t('First name').'*', 'firstname');
        $html .= $this->input(Locale::t('Last name').'*', 'secondname');
        $html .= $this->input(Locale::t('Login').'*', 'username');
        $html .= $this->input(Locale::t('Password').'*', 'password');
        $html .= $this->input(Locale::t('Email').'*', 'email');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('site_name', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerNoTags('site_name', Locale::t('Invalid character detected'));
        $validator->registerUtf8('site_name', Locale::t('Invalid character detected'));
        $validator->registerString('site_slogan', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerNoTags('site_slogan', Locale::t('Invalid character detected'));
        $validator->registerUtf8('site_slogan', Locale::t('Invalid character detected'));
        $validator->registerEmail('email_from', true, Locale::t('Invalid email'));
        $validator->registerString('secret', 8, 255, true, Locale::t('Secret key is too short'));
        $validator->registerString('firstname', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerNoTags('firstname', Locale::t('Invalid character detected'));
        $validator->registerUtf8('firstname', Locale::t('Invalid character detected'));
        $validator->registerString('secondname', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerNoTags('secondname', Locale::t('Invalid character detected'));
        $validator->registerUtf8('secondname', Locale::t('Invalid character detected'));
        $validator->registerString('username', \Zira\User::LOGIN_MIN_CHARS, \Zira\User::LOGIN_MAX_CHARS, true, Locale::t('Invalid username'));
        $validator->registerRegexp('username', \Zira\User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        $validator->registerString('password', \Zira\User::PASSWORD_MIN_CHARS, \Zira\User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
        $validator->registerRegexp('password', \Zira\User::REGEXP_PASSWORD, Locale::t('Password contain bad characters'));
        $validator->registerEmail('email', true, Locale::t('Invalid email'));
    }
}