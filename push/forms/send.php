<?php
/**
 * Zira project.
 * send.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Send extends Form
{
    const TITLE_MAX_SIZE = 50;
    const BODY_MAX_SIZE = 120;
    
    protected $_id = 'push-send-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-4';

    protected $_checkbox_inline_label = true;
    
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
        $html .= $this->hidden('offset', array('class'=>'push-offset'));
        $html .= $this->hidden('language', array('class'=>'push-language'));
        $html .= $this->input(Locale::t('Title').'*', 'title', array('placeholder'=>Zira\Locale::t('max. %s characters', self::TITLE_MAX_SIZE), 'class'=>'form-control title_input'));
        $html .= $this->input(Locale::t('Description'), 'description', array('placeholder'=>Zira\Locale::t('max. %s characters', self::BODY_MAX_SIZE), 'class'=>'form-control body_input'));
        $html .= $this->input(Locale::t('Image'), 'image', array('class'=>'form-control image_input'));
        $html .= $this->input(Locale::t('URL'), 'url', array('class'=>'form-control url_input'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'col-sm-offset-4 col-sm-8'));
        $html .= Zira\Helper::tag('div', null, array('class'=>'push-preview'));
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerString('title',null,self::TITLE_MAX_SIZE,true,Locale::tm('Invalid value "%s"', 'dash', Locale::tm('Title','dash')));
        $validator->registerNoTags('title',Locale::tm('Invalid value "%s"','dash',Locale::tm('Title','dash')));
        $validator->registerUtf8('title',Locale::tm('Invalid value "%s"','dash',Locale::tm('Title','dash')));
        
        $validator->registerString('description',null,self::BODY_MAX_SIZE,false,Locale::tm('Invalid value "%s"','dash',Locale::tm('Description','dash')));
        $validator->registerNoTags('description',Locale::tm('Invalid value "%s"','dash',Locale::tm('Description','dash')));
        $validator->registerUtf8('description',Locale::tm('Invalid value "%s"','dash',Locale::tm('Description','dash')));
        
        $validator->registerString('image',null,255,false,Locale::tm('Invalid value "%s"','dash',Locale::tm('Image','dash')));
        $validator->registerNoTags('image',Locale::tm('Invalid value "%s"','dash',Locale::tm('Image','dash')));
        $validator->registerUtf8('image',Locale::tm('Invalid value "%s"','dash',Locale::tm('Image','dash')));

        $validator->registerString('url',null,255,false,Locale::tm('Invalid value "%s"','dash',Locale::tm('URL','dash')));
        $validator->registerNoTags('url',Locale::tm('Invalid value "%s"','dash',Locale::tm('URL','dash')));
        $validator->registerUtf8('url',Locale::tm('Invalid value "%s"','dash',Locale::tm('URL','dash')));
    }
}