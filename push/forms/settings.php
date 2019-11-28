<?php
/**
 * Zira project.
 * settings.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'push-settings-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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

        $html .= $this->input(Locale::tm('Private key','push'), 'push_priv_key', array('class' => 'form-control priv-key-input'));
        $html .= $this->input(Locale::tm('Public key','push'), 'push_pub_key', array('class' => 'form-control pub-key-input'));
        $html .= $this->checkbox(Locale::tm('Enable subscription request on page load','push'), 'push_subscribe_onload_on', null, false);
        
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('push_priv_key',null,255,true,Locale::t('Invalid value "%s"',Locale::tm('Private key','push')));
        $validator->registerNoTags('push_priv_key',Locale::t('Invalid value "%s"',Locale::tm('Private key','push')));
        $validator->registerUtf8('push_priv_key',Locale::t('Invalid value "%s"',Locale::tm('Private key','push')));
        $validator->registerString('push_pub_key',null,255,true,Locale::t('Invalid value "%s"',Locale::tm('Public key','push')));
        $validator->registerNoTags('push_pub_key',Locale::t('Invalid value "%s"',Locale::tm('Public key','push')));
        $validator->registerUtf8('push_pub_key',Locale::t('Invalid value "%s"',Locale::tm('Public key','push')));
    }
}