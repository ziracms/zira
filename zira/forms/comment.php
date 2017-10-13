<?php
/**
 * Zira project.
 * comment.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms;

use Zira\Config;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\User;
use Zira\View;

class Comment extends Form {
    const MAX_CHARS = 1024;

    protected $_id = 'comment-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        View::addParser();
        $this->setAjax(true);
        $this->setUrl('comment');
        $this->setTitle(Locale::t('Leave a comment'));
        $this->setDescription(Locale::t('Message should contain at least %s characters', Config::get('comment_min_chars', 10)));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(){ zira_reset_comments_form(true); }); });';
        $script .= Helper::tag_close('script');
        //View::addHTML($script, View::VAR_BODY_BOTTOM);
        View::addBodyBottomScript($script);
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->hidden('record_id');
        $html .= $this->hidden('parent_id');
        $html .= $this->hidden('reply_id');
        $html .= Helper::tag('div',null,array('class'=>'form-group comment-reply-preview'));
        if(!User::isAuthorized()) {
            $html .= $this->input(Locale::t('Name'), 'sender_name');
        } else {
            $html .= $this->hidden('sender_name');
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

        $html .= $this->textarea(Locale::t('Message').'*','comment', array('class'=>'form-control user-rich-input'));
        if (Config::get('comments_captcha',true)) {
            $html .= $this->captcha(Locale::t('Enter result').'*');
        } else {
            $html .= $this->captchaLazy(Locale::t('Enter result') . '*');
        }
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        if (Config::get('comments_captcha',true)) {
            $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        } else {
            $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        }
        $validator->registerNumber('record_id', null, null, true, Locale::t('An error occurred'));
        $validator->registerString('sender_name', 2, 255, false, Locale::t('Invalid name'));
        $validator->registerNoTags('sender_name', Locale::t('Invalid name'));
        $validator->registerUtf8('sender_name', Locale::t('Invalid name'));
        //$validator->registerText('comment', Config::get('comment_min_chars', 10), true, Locale::t('Message should contain at least %s characters', Config::get('comment_min_chars', 10)));
        $validator->registerCustom(array(get_class(), 'checkCommentMinLength'), 'comment', Locale::t('Message should contain at least %s characters', Config::get('comment_min_chars', 10)));
        //$validator->registerString('comment', null, self::MAX_CHARS, true, Locale::t('Sorry, your comment is too big'));
        $validator->registerCustom(array(get_class(), 'checkCommentMaxLength'), 'comment', Locale::t('Sorry, your comment is too big'));
        $validator->registerNoTags('comment', Locale::t('Message contains bad character'));
        //$validator->registerUtf8('comment', Locale::t('Message contains bad character'));
    }

    public static function checkCommentMinLength($comment) {
        return (mb_strlen(html_entity_decode($comment), CHARSET)>=Config::get('comment_min_chars', 10));
    }

    public static function checkCommentMaxLength($comment) {
        return (mb_strlen(html_entity_decode($comment), CHARSET)<=self::MAX_CHARS);
    }
}