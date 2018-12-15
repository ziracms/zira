<?php
/**
 * Zira project.
 * commentsettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Commentsettings extends Form
{
    protected $_id = 'dash-commentsettings-form';

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
        $html .= $this->input(Locale::t('Nesting level'), 'comments_max_nesting', array('placeholder'=>'1 - 10'));
        $html .= $this->input(Locale::t('Comments limit'), 'comments_limit');
        $html .= $this->input(Locale::t('Comment min. length'), 'comment_min_chars');
        $html .= $this->checkbox(Locale::t('Moderation'), 'comment_moderate', null, false);
        $html .= $this->checkbox(Locale::t('Anonymous comments'), 'comment_anonymous', null, false);
        $html .= $this->input(Locale::t('Notification Email'), 'comment_notify_email');
        $html .= $this->checkbox(Locale::t('Allow commenting'), 'comments_allowed', null, false);
        $html .= $this->checkbox(Locale::t('Always show CAPTCHA for not authorized users'), 'comments_captcha', null, false);
        $html .= $this->checkbox(Locale::t('Always show CAPTCHA for authorized users'), 'comments_captcha_users', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerNumber('comments_max_nesting',1,10,true,Locale::t('Invalid value "%s"',Locale::t('Nesting level')));
        $validator->registerNumber('comments_limit',1,null,true,Locale::t('Invalid value "%s"',Locale::t('Comments limit')));
        $validator->registerNumber('comment_min_chars',1,null,true,Locale::t('Invalid value "%s"',Locale::t('Comment min. length')));
        $validator->registerEmail('comment_notify_email',false,Locale::t('Invalid value "%s"',Locale::t('Notification Email')));
    }
}