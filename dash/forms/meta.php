<?php
/**
 * Zira project.
 * meta.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Meta extends Form
{
    protected $_id = 'dash-meta-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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
        $html .= $this->select(Locale::t('Default layout').'*','layout',Zira\View::getLayouts());
        $html .= $this->input(Locale::t('Website title'), 'site_name', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Website slogan'), 'site_slogan', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Website logo'), 'site_logo', array('class'=>'form-control logo_option','style'=>'padding-right:40px'));
        $html .= $this->input(Locale::t('Website icon'), 'site_favicon', array('class'=>'form-control favicon_option','style'=>'padding-right:40px'));
        $html .= $this->input(Locale::t('Records limit'), 'records_limit');
        $html .= $this->input(Locale::t('Records limit for widgets'), 'widget_records_limit');
        $html .= $this->checkbox(Locale::t('Enable comments'), 'comments_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Show child category records'), 'category_childs_list', null, false);
        $html .= $this->checkbox(Locale::t('Enable breadcrumbs'), 'enable_breadcrumbs', null, false);
        $html .= $this->checkbox(Locale::t('Enable paginator'), 'enable_pagination', null, false);
        $html .= $this->input(Locale::t('Window title'), 'site_title', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Keywords'), 'site_keywords', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Description'), 'site_description', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Copyright string'), 'site_copyright', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->checkbox(Locale::t('Show link to developer\'s website'), 'dev_copyright', null, false);
        $html .= $this->checkbox(Locale::t('Show website name in window title'), 'site_window_title', null, false);
        $html .= $this->checkbox(Locale::t('Enable scroll effects'), 'site_scroll_effects', null, false);
        $html .= $this->checkbox(Locale::t('Show image descriptions'), 'site_parse_images', null, false);
        $html .= $this->radioButton(Locale::t('Record columns count'), 'site_records_grid', array('0'=>'1','1'=>'2','2'=>'3','3'=>'4','4'=>'5'));
        $html .= $this->select(Locale::t('Sort records'), 'records_sorting', array('id'=>Locale::t('by creation date'),'rating'=>Locale::t('by rating'),'comments'=>Locale::t('by comments count')));
        $html .= $this->input(Locale::t('Carousel thumbs width'), 'carousel_thumbs_width', array('placeholder'=>Recordsettings::THUMB_MIN_SIZE.' - '.Recordsettings::THUMB_MAX_SIZE));
        $html .= $this->input(Locale::t('Carousel thumbs height'), 'carousel_thumbs_height', array('placeholder'=>Recordsettings::THUMB_MIN_SIZE.' - '.Recordsettings::THUMB_MAX_SIZE));
        
        $html .= Zira\Helper::tag_open('div', array('id'=>'dashmetaform_access_button'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'col-sm-offset-4 col-sm-8'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'checkbox checkbox-float'));
        $html .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-menu-right', 'style'=>'float:left;top:3px'));
        $html .= Zira\Helper::tag_open('label', array('class' => 'col-sm-4 control-label', 'style'=>'width:auto;padding-top:0;padding-left:7px;', 'id'=>'dashmetaform_access_label'));
        $html .= Locale::t('Restrict access');
        $html .= Zira\Helper::tag_close('label');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_open('div', array('id'=>'dashmetaform_access_container', 'style'=>'display:none'));
        $html .= $this->checkbox(Locale::t('Restrict access to gallery'), 'gallery_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to files'), 'files_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to audio'), 'audio_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to video'), 'video_check', null, false);
        $html .= Zira\Helper::tag_close('div');
        
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('site_name',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Website title')));
        $validator->registerNoTags('site_name',Locale::t('Invalid value "%s"',Locale::t('Website title')));
        $validator->registerUtf8('site_name',Locale::t('Invalid value "%s"',Locale::t('Website title')));
        $validator->registerString('site_slogan',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Website slogan')));
        $validator->registerNoTags('site_slogan',Locale::t('Invalid value "%s"',Locale::t('Website slogan')));
        $validator->registerUtf8('site_slogan',Locale::t('Invalid value "%s"',Locale::t('Website slogan')));
        $validator->registerString('site_logo',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Website logo')));
        $validator->registerCustom(array(get_class(), 'checkLogo'), 'site_logo',Locale::t('Invalid value "%s"',Locale::t('Website logo')));
        $validator->registerString('site_favicon',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Website icon')));
        $validator->registerCustom(array(get_class(), 'checkIcon'), 'site_favicon',Locale::t('Invalid value "%s"',Locale::t('Website icon')));
        $validator->registerNumber('records_limit',1,null,true,Locale::t('Invalid value "%s"',Locale::t('Records limit')));
        $validator->registerNumber('widget_records_limit',1,null,true,Locale::t('Invalid value "%s"',Locale::t('Records limit for widgets')));
        $validator->registerString('site_title',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerNoTags('site_title',Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerUtf8('site_title',Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerString('site_keywords',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerNoTags('site_keywords',Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerUtf8('site_keywords',Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerString('site_description',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerNoTags('site_description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('site_description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerString('site_copyright',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Copyright string')));
        $validator->registerUtf8('site_copyright',Locale::t('Invalid value "%s"',Locale::t('Copyright string')));
        $validator->registerString('layout', 0, 0, true, Locale::t('Invalid value "%s"',Locale::t('Default layout')));
        $validator->registerCustom(array(get_class(), 'checkLayout'), 'layout', Locale::t('Invalid value "%s"',Locale::t('Default layout')));
        $validator->registerNumber('carousel_thumbs_width',Recordsettings::THUMB_MIN_SIZE,Recordsettings::THUMB_MAX_SIZE,true,Locale::t('Invalid value "%s"',Locale::t('Carousel thumbs width')));
        $validator->registerNumber('carousel_thumbs_height',Recordsettings::THUMB_MIN_SIZE,Recordsettings::THUMB_MAX_SIZE,true,Locale::t('Invalid value "%s"',Locale::t('Carousel thumbs height')));
        
        $logo = $this->getValue('site_logo');
        if (!empty($logo)) {
            $logo = trim($logo,'/');
        }
        $icon = $this->getValue('site_favicon');
        if (!empty($icon)) {
            $icon = trim($icon,'/');
        } else {
            $icon = 'favicon.ico';
        }
        $this->updateValues(array(
            'site_logo' => $logo,
            'site_favicon' => $icon
        ));
    }

    public static function checkLogo($logo) {
        if (empty($logo)) return true;
        if (strpos($logo,'..')!==false) return false;

        $p = strrpos($logo, '.');
        if ($p===false) return false;
        $ext = substr($logo, $p+1);
        if (!in_array(strtolower($ext), array('jpg','jpeg','gif','png'))) return false;

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $logo)) return false;

        $size = @getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $logo);
        if (!$size) return false;

        return true;
    }
    
    public static function checkIcon($icon) {
        if (empty($icon)) return true;
        if (strpos($icon,'..')!==false) return false;

        $p = strrpos($icon, '.');
        if ($p===false) return false;
        $ext = substr($icon, $p+1);
        if (!in_array(strtolower($ext), array('jpg','jpeg','gif','png','ico'))) return false;

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $icon)) return false;

        return true;
    }

    public static function checkLayout($layout) {
        $layouts = Zira\View::getLayouts();
        return array_key_exists($layout, $layouts);
    }
}