<?php
/**
 * Zira project.
 * conversation.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\User;
use Zira\View;

class Conversation extends Form {
    protected $_id = 'user-conversation-form';

    protected $_recipient;

    public function __construct(\Zira\Models\User $recipient) {
        $this->_recipient = $recipient;
        parent::__construct($this->_id);
    }

    protected function _init() {
        View::addParser();
        $this->setAjax(true);
        $this->setTitle(Locale::t('New message'));
        $this->setDescription(Locale::t('Message to: %s', User::getProfileName($this->_recipient)));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(evnt, response){ jQuery(\'#'.$this->getId().'\').get(0).reset(); zira_user_message_sent_success(response); }); });';
        $script .= Helper::tag_close('script');
        //View::addHTML($script, View::VAR_BODY_BOTTOM);
        View::addBodyBottomScript($script);
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Subject').'*','subject');
        $html .= $this->textarea(Locale::t('Message').'*', 'content', array('class'=>'form-control user-rich-input'));
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('subject', null, 255, true, Locale::t('Please specify the subject of your message'));
        $validator->registerNoTags('subject', Locale::t('Subject contains bad character'));
        $validator->registerUtf8('subject', Locale::t('Subject contains bad character'));
        $validator->registerText('content', \Zira\Models\Message::MIN_CHARS, true, Locale::t('Message should contain at least %s characters', \Zira\Models\Message::MIN_CHARS));
        $validator->registerNoTags('content', Locale::t('Message contains bad character'));
        //$validator->registerUtf8('content', Locale::t('Message contains bad character'));
    }
}