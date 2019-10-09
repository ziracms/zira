<?php
/**
 * Zira project.
 * mailing.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Mailing extends Form
{
    protected $_id = 'dash-mailing-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';

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
        $html .= $this->hidden('offset', array('class'=>'dash-mailing-offset'));
        $html .= $this->hidden('type', array('class'=>'dash-mailing-type'));
        $html .= $this->hidden('language', array('class'=>'dash-mailing-language'));
        $html .= $this->input(Locale::t('Subject') . '*', 'subject', array('class'=>'form-control dash-mailing-subject','placeholder'=>Locale::t('max. length: %s chars', 255)));
        $html .= $this->textarea(Locale::t('Message') . '*', 'message', array('class'=>'form-control dash-mailing-message','rows'=>8,'title'=>Locale::t('Supported variables: %s','$user')));
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerNumber('offset',0,null,true,Locale::t('An error occurred'));
        $validator->registerString('subject', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Subject')));
        $validator->registerNoTags('subject', Locale::t('Invalid value "%s"',Locale::t('Subject')));
        $validator->registerUtf8('subject', Locale::t('Invalid value "%s"',Locale::t('Subject')));
        $validator->registerNoTags('message', Locale::t('Invalid value "%s"',Locale::t('Message')));
        $validator->registerUtf8('message', Locale::t('Invalid value "%s"',Locale::t('Message')));
    }
}