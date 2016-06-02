<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\View;

class Message extends Form {
    protected $_id = 'user-message-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setAjax(true);
        $this->setTitle(Locale::t('Reply'));
        $this->setDescription(Locale::t('Message should contain at least %s characters', \Zira\Models\Message::MIN_CHARS));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(){ jQuery(\'#'.$this->getId().'\').get(0).reset(); }); });';
        $script .= Helper::tag_close('script');
        View::addHTML($script, View::VAR_BODY_BOTTOM);
    }

    protected function _render() {
        $html = $this->open();

        $extra_items = \Zira\Hook::run(\Zira\Page::USER_TEXTAREA_HOOK);
        if (!empty($extra_items)) {
            $html .= Helper::tag_open('div',array('class'=>'user-text-form-extra-items'));
            foreach($extra_items as $item) {
                $html .= Helper::tag_open('div',array('class'=>'user-text-form-extra-item'));
                $html .= $item;
                $html .= Helper::tag_close('div');
            }
            $html .= Helper::tag_close('div');
        }

        $html .= $this->textarea(Locale::t('Message').'*', 'content');
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerText('content', \Zira\Models\Message::MIN_CHARS, true, Locale::t('Message should contain at least %s characters', \Zira\Models\Message::MIN_CHARS));
        $validator->registerNoTags('content', Locale::t('Message contains bad character'));
    }
}