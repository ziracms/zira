<?php
/**
 * Zira project.
 * mailsettings.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Mailsettings extends Form
{
    protected $_id = 'dash-mailsettings-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';

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
        $html .= $this->input(Locale::t('Send from email'), 'email_from');
        $html .= $this->input(Locale::t('Send from name'), 'email_from_name', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->checkbox(Locale::t('Use SMTP server'), 'use_smtp', null, false);
        $html .= $this->input(Locale::t('SMTP host'), 'smtp_host');
        $html .= $this->input(Locale::t('SMTP port'), 'smtp_port');
        $html .= $this->input(Locale::t('SMTP connection'), 'smtp_secure');
        $html .= $this->input(Locale::t('SMTP username'), 'smtp_username');
        $html .= $this->input(Locale::t('SMTP password'), 'smtp_password');
        $html .= $this->textarea(Locale::t('Email confirmation message'), 'user_email_confirmation_message', array('title'=>Locale::t('Supported variables: %s','$user, $code, $url, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->textarea(Locale::t('Password recovery message'), 'user_password_recovery_message', array('title'=>Locale::t('Supported variables: %s','$user, $code, $url, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->textarea(Locale::t('New password message'), 'user_new_password_message', array('title'=>Locale::t('Supported variables: %s','$user, $code, $url, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->textarea(Locale::t('New comment message'), 'comment_notification_message', array('title'=>Locale::t('Supported variables: %s','$page, $url, $comment, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->textarea(Locale::t('Feedback message'), 'feedback_message', array('title'=>Locale::t('Supported variables: %s','$name, $email, $message, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->textarea(Locale::t('New message notification'), 'new_message_notification', array('title'=>Locale::t('Supported variables: %s','$user, $sender, $url, $site'), 'placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('email_from',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Send from email')));
        $validator->registerEmail('email_from',true,Locale::t('Invalid value "%s"',Locale::t('Send from email')));
        $validator->registerString('email_from_name',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Send from name')));
        $validator->registerNoTags('email_from_name',Locale::t('Invalid value "%s"',Locale::t('Send from name')));
        $validator->registerUtf8('email_from_name',Locale::t('Invalid value "%s"',Locale::t('Send from name')));
        $validator->registerString('smtp_host',null,255,false,Locale::t('Invalid value "%s"',Locale::t('SMTP host')));
        $validator->registerUtf8('smtp_host',Locale::t('Invalid value "%s"',Locale::t('SMTP host')));
        $validator->registerNumber('smtp_port',null,null,false,Locale::t('Invalid value "%s"',Locale::t('SMTP port')));
        $validator->registerString('smtp_secure',null,255,false,Locale::t('Invalid value "%s"',Locale::t('SMTP connection')));
        $validator->registerUtf8('smtp_secure',Locale::t('Invalid value "%s"',Locale::t('SMTP connection')));
        $validator->registerString('smtp_username',null,255,false,Locale::t('Invalid value "%s"',Locale::t('SMTP username')));
        $validator->registerUtf8('smtp_username',Locale::t('Invalid value "%s"',Locale::t('SMTP username')));
        $validator->registerString('smtp_password',null,255,false,Locale::t('Invalid value "%s"',Locale::t('SMTP password')));
        $validator->registerUtf8('smtp_password',Locale::t('Invalid value "%s"',Locale::t('SMTP password')));
        $validator->registerString('user_email_confirmation_message',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Email confirmation message')));
        $validator->registerUtf8('user_email_confirmation_message',Locale::t('Invalid value "%s"',Locale::t('Email confirmation message')));
        $validator->registerString('user_password_recovery_message',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Password recovery message')));
        $validator->registerUtf8('user_password_recovery_message',Locale::t('Invalid value "%s"',Locale::t('Password recovery message')));
        $validator->registerString('user_new_password_message',null,255,false,Locale::t('Invalid value "%s"',Locale::t('New password message')));
        $validator->registerUtf8('user_new_password_message',Locale::t('Invalid value "%s"',Locale::t('New password message')));
        $validator->registerString('comment_notification_message',null,255,false,Locale::t('Invalid value "%s"',Locale::t('New comment message')));
        $validator->registerUtf8('comment_notification_message',Locale::t('Invalid value "%s"',Locale::t('New comment message')));
        $validator->registerString('feedback_message',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Feedback message')));
        $validator->registerUtf8('feedback_message',Locale::t('Invalid value "%s"',Locale::t('Feedback message')));
        $validator->registerString('new_message_notification',null,255,false,Locale::t('Invalid value "%s"',Locale::t('New message notification')));
        $validator->registerUtf8('new_message_notification',Locale::t('Invalid value "%s"',Locale::t('New message notification')));
    }
}