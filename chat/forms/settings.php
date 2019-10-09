<?php
/**
 * Zira project.
 * settings.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Chat\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'dash-chat-settings-form';

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
        $html .= $this->input(Locale::tm('Message retention period (in days)', 'chat').'*', 'chat_trash_time');
        $html .= $this->checkbox(Locale::t('Always show CAPTCHA'), 'chat_captcha', null, false);
        $html .= $this->checkbox(Locale::t('Always show CAPTCHA for authorized users'), 'chat_captcha_users', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerNumber('chat_trash_time',0,null,true,Locale::t('Invalid value "%s"',Locale::tm('Message retention period (in days)', 'chat')));
    }
}