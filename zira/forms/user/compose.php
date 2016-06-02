<?php
/**
 * Zira project.
 * compose.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\View;

class Compose extends Form {
    const MAX_RECIPIENTS = 10;

    protected $_id = 'user-compose-form';
    protected static $_users = array();

    public function __construct() {
        parent::__construct($this->_id);
    }

    public function getUsers() {
        return self::$_users;
    }

    protected function _init() {
        $this->setAjax(true);
        $this->setTitle(Locale::t('New message'));
        $this->setDescription(Locale::t('Please enter user login, full name or ID'));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){';
        $script .= 'jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(){';
        $script .= 'jQuery(\'#'.$this->getId().'\').get(0).reset();';
        $script .= '});';
        $script .= 'jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-error\', function(){';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_input\').each(function(){';
        $script .= 'var text = jQuery(this).data(\'autocomplete_text\');';
        $script .= 'if (typeof(text)!="undefined" && text) jQuery(this).val(text);';
        $script .= '});';
        $script .= '});';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_input\').parent().append(\'<a class="zira_form_compose_remove_recipient_input" href="javascript:void(0)"><span class="glyphicon glyphicon-minus-sign"></span></a>\');';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_btn\').click(function(e){';
        $script .= 'e.stopPropagation(); e.preventDefault();';
        $script .= 'if (jQuery(\'.zira_form_compose_add_recipient_input\').length>='.self::MAX_RECIPIENTS.') return;';
        $script .= 'var con = jQuery(\'.zira_form_compose_add_recipient_input\').last().parents(\'.form-group\');';
        $script .= '$(con).after(\'<div class="form-group form-group-added">\'+$(con).html()+\'</div>\');';
        $script .= 'if (jQuery(\'.zira_form_compose_add_recipient_input\').length>='.self::MAX_RECIPIENTS.'){';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_btn\').attr(\'disabled\',\'disabled\');';
        $script .= '}';
        $script .= '});';
        $script .= 'jQuery(\'.container #content\').on(\'click\', \'.zira_form_compose_remove_recipient_input\', function(e){';
        $script .= 'jQuery(this).parents(\'.form-group\').remove();';
        $script .= 'if (jQuery(\'.zira_form_compose_add_recipient_input\').length<'.self::MAX_RECIPIENTS.'){';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_btn\').removeAttr(\'disabled\');';
        $script .= '}';
        $script .= '});';
        $script .= 'jQuery(\'#'.$this->getId().'\').submit(function(){';
        $script .= 'jQuery(\'.zira_form_compose_add_recipient_input\').each(function(){';
        $script .= 'var id = jQuery(this).data(\'autocomplete_id\');';
        $script .= 'if (typeof(id)!="undefined" && id) jQuery(this).val(id);';
        $script .= '});';
        $script .= '});';
        $script .= '});';
        $script .= Helper::tag_close('script');
        View::addHTML($script, View::VAR_BODY_BOTTOM);
        View::addAutoCompleter();
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('Recipient').'*','users[]',array('class'=>'form-control form-input-autocomplete zira_form_compose_add_recipient_input','data-url'=>Zira\Helper::url('user/autocomplete'),'data-token'=>Zira\User::getToken()));
        $html .= $this->button(Locale::t('Add recipient'), array('class'=>'btn btn-default zira_form_compose_add_recipient_btn','type'=>'button'));
        $html .= $this->input(Locale::t('Subject').'*','subject');

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
        $validator->registerString('subject', null, 255, true, Locale::t('Please specify the subject of your message'));
        $validator->registerNoTags('subject', Locale::t('Subject contains bad character'));
        $validator->registerText('content', \Zira\Models\Message::MIN_CHARS, true, Locale::t('Message should contain at least %s characters', \Zira\Models\Message::MIN_CHARS));
        $validator->registerNoTags('content', Locale::t('Message contains bad character'));
        $validator->registerCustom(array(get_class(), 'checkUsers'), 'users', Locale::t('Please enter correct user login, full name or ID'));
    }

    public static function checkUsers($users) {
        if (!is_array($users) || empty($users)) return false;
        if (count($users)>self::MAX_RECIPIENTS) return false;
        $added = array();
        foreach($users as $user_id) {
            if (empty($user_id)) {
                return false;
            } else if (is_numeric($user_id)) {
                $user = new Zira\Models\User($user_id);
                if (!$user->loaded() || !$user->active) return false;
            } else if (strpos($user_id, ' ')>0) {
                $parts = explode(' ', $user_id);
                if (count($parts)!=2) return false;
                $_user = Zira\Models\User::getCollection()
                            ->where('firstname','=',$parts[0])
                            ->and_where('secondname','=',$parts[1])
                            ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                            ->order_by('id','asc')
                            ->limit(1)
                            ->get(0, true);
                if (!$_user) return false;
                $user = new Zira\Models\User();
                $user->loadFromArray($_user);
            } else {
                $_user = Zira\Models\User::getCollection()
                            ->where('username','=',$user_id)
                            ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                            ->limit(1)
                            ->order_by('id','asc')
                            ->get(0, true);
                if (!$_user) return false;
                $user = new Zira\Models\User();
                $user->loadFromArray($_user);
            }
            if (Zira\User::isSelf($user)) return false;
            if (in_array($user->id, $added)) continue;
            self::$_users []= $user;
            $added []= $user->id;
        }
        return true;
    }
}