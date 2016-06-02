<?php
/**
 * Zira project.
 * usersettings.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Usersettings extends Form
{
    protected $_id = 'dash-usersettings-form';

    protected $_label_class = 'col-sm-5 control-label';
    protected $_input_wrap_class = 'col-sm-7';
    protected $_input_offset_wrap_class = 'col-sm-offset-5 col-sm-7';

    protected $_checkbox_inline_label = false;

    public function __construct()
    {
        parent::__construct($this->_id);
    }

    protected function _init()
    {
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal dash-window-form');
    }

    protected function _render()
    {
        $html = $this->open();
        $html .= $this->input(Locale::t('Photo min. width'), 'user_photo_min_width', array('placeholder'=>'10 - 400'));
        $html .= $this->input(Locale::t('Photo min. height'), 'user_photo_min_height', array('placeholder'=>'10 - 300'));
        $html .= $this->input(Locale::t('Photo max. width'), 'user_photo_max_width', array('placeholder'=>'400 - 1920'));
        $html .= $this->input(Locale::t('Photo max. height'), 'user_photo_max_height', array('placeholder'=>'300 - 1080'));
        $html .= $this->input(Locale::t('Thumb width'), 'user_thumb_width', array('placeholder'=>'10 - 200'));
        $html .= $this->input(Locale::t('Thumb height'), 'user_thumb_height', array('placeholder'=>'10 - 200'));
        $html .= $this->checkbox(Locale::t('Allow sign-up'), 'user_signup_allow', null, false);
        $html .= $this->checkbox(Locale::t('Access user profiles'), 'user_profile_view_allow', null, false);
        $html .= $this->checkbox(Locale::t('Allow login change'), 'user_login_change_allow', null, false);
        $html .= $this->checkbox(Locale::t('Verify email'), 'user_email_verify', null, false);
        $html .= $this->checkbox(Locale::t('Check user browser'), 'user_check_ua', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerNumber('user_photo_min_width',10,400,true,Locale::t('Invalid value "%s"',Locale::t('Photo min. width')));
        $validator->registerNumber('user_photo_min_height',10,300,true,Locale::t('Invalid value "%s"',Locale::t('Photo min. height')));
        $validator->registerNumber('user_photo_max_width',400,1920,true,Locale::t('Invalid value "%s"',Locale::t('Photo max. width')));
        $validator->registerNumber('user_photo_max_height',300,1080,true,Locale::t('Invalid value "%s"',Locale::t('Photo max. height')));
        $validator->registerNumber('user_thumb_width',10,200,true,Locale::t('Invalid value "%s"',Locale::t('Thumb width')));
        $validator->registerNumber('user_thumb_height',10,200,true,Locale::t('Invalid value "%s"',Locale::t('Thumb height')));
    }
}