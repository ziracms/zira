<?php
/**
 * Zira project.
 * compose.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Forms;

use Zira\Config;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\View;

class Compose extends Form {
    protected $_id = 'forum-message-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setAjax(true);
        $this->setTitle(Locale::tm('New thread', 'forum'));
        $this->setDescription(Locale::t('Message should contain at least %s characters', Config::get('forum_min_chars', 10)));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){';
        $script .= 'jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(e, response){ ';
        $script .= 'zira_forum_form_submit_success(response);';
        $script .= '});';
        $script .= '});';
        $script .= Helper::tag_close('script');
        View::addHTML($script, View::VAR_BODY_BOTTOM);
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::tm('Title', 'forum').'*', 'title');
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

        $html .= $this->textarea(Locale::t('Message').'*','message', array('class'=>'form-control user-rich-input', 'rows'=>6));
        $html .= $this->captcha(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('title', 0, 255, true, Locale::tm('Invalid thread title', 'forum'));
        $validator->registerNoTags('title', Locale::tm('Invalid thread title', 'forum'));
        $validator->registerUtf8('title', Locale::tm('Invalid thread title', 'forum'));
        $validator->registerCustom(array(get_class(), 'checkMessageMinLength'), 'message', Locale::t('Message should contain at least %s characters', Config::get('forum_min_chars', 10)));
        $validator->registerNoTags('message', Locale::t('Message contains bad character'));
        //$validator->registerUtf8('message', Locale::t('Message contains bad character'));
    }

    public static function checkMessageMinLength($message) {
        return (mb_strlen(html_entity_decode($message), CHARSET)>=Config::get('forum_min_chars', 10));
    }
}