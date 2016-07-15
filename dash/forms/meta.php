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
        $html .= $this->input(Locale::t('Website logo'), 'site_logo', array('class'=>'form-control logo_option'));
        $html .= $this->input(Locale::t('Records limit'), 'records_limit');
        $html .= $this->input(Locale::t('Records limit for widgets'), 'widget_records_limit');
        $html .= $this->checkbox(Locale::t('Show child category records'), 'category_childs_list', null, false);
        $html .= $this->input(Locale::t('Window title'), 'site_title', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Keywords'), 'site_keywords', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Description'), 'site_description', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->input(Locale::t('Copyright string'), 'site_copyright', array('placeholder'=>Locale::t('max. %s characters', 255)));
        $html .= $this->checkbox(Locale::t('Show website name in window title'), 'site_window_title', null, false);
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

        $logo = $this->getValue('site_logo');
        if (!empty($logo)) {
            $logo = trim($logo,'/');
            $this->updateValues(array(
                'site_logo' => $logo
            ));
        }
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

    public static function checkLayout($layout) {
        $layouts = Zira\View::getLayouts();
        return array_key_exists($layout, $layouts);
    }
}