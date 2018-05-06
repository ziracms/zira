<?php
/**
 * Zira project.
 * submit.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Forms;

use Zira\Config;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\User;
use Zira\View;

class Submit extends Form {
    protected $_id = 'widget-chat-form';
    
    protected $_label_class = 'col-sm-12 control-label';
    protected $_input_wrap_class = 'col-sm-12';
    protected $_input_offset_wrap_class = 'col-sm-offset-0 col-sm-12';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setUrl('chat/index/submit?'.FORMAT_GET_VAR.'='.FORMAT_JSON);
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal xhr-form');
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->hidden('chat_id');
        if(!User::isAuthorized()) {
            $html .= $this->input(Locale::t('Name'), 'sender_name', array('id'=>'chat-'.intval($this->getValue('chat_id')).'-sender-name'));
        } else {
            $html .= $this->hidden('sender_name', array('id'=>'chat-'.intval($this->getValue('chat_id')).'-sender-name'));
        }

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

        $html .= $this->textarea(Locale::tm('Your message','chat'),'message', array('class'=>'form-control user-rich-input'));
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerNumber('chat_id', null, null, true, Locale::t('An error occurred'));
        $validator->registerString('sender_name', 1, 255, false, Locale::t('Invalid name'));
        $validator->registerNoTags('sender_name', Locale::t('Invalid name'));
        $validator->registerUtf8('sender_name', Locale::t('Invalid name'));
        $validator->registerString('message', null, \Chat\Chat::MAX_CHARS, true, Locale::tm('Incorrect message length', 'chat'));
        $validator->registerNoTags('message', Locale::t('Message contains bad character'));
    }
}