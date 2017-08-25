<?php
/**
 * Zira project.
 * options.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Options extends Form
{
    protected $_id = 'dash-options-form';

    protected $_label_class = 'col-sm-5 control-label';
    protected $_input_wrap_class = 'col-sm-7';
    protected $_input_offset_wrap_class = 'col-sm-offset-5 col-sm-7';
    protected $_select_wrapper_class = 'col-sm-7';

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
        $caching_supported = is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR);
        if (!$caching_supported) $caching_attr = array('disabled' => 'disabled');
        else $caching_attr = null;

        $gzip_supported = function_exists('gzencode');
        if (!$gzip_supported) $gzip_attr = array('disabled' => 'disabled');
        else $gzip_attr = null;

        $timezones = array();
        $timezone_list = timezone_identifiers_list();
        foreach($timezone_list as $timezone) {
            $timezones[$timezone] = Locale::t($timezone);
        }

        $html = $this->open();
        $html .= $this->select(Locale::t('Timezone'), 'timezone', $timezones);
        $html .= $this->input(Locale::t('Watermark'), 'watermark', array('class'=>'form-control watermark_option'));
        $html .= $this->input(Locale::t('Watermark margin'), 'watermark_margin', array('class'=>'form-control'));
        $html .= $this->checkbox(Locale::t('Enable watermark'), 'watermark_enabled', null, false);
        $html .= $this->input(Locale::t('PHP date format'), 'date_format', array('placeholder'=>'d.m.Y'));
        $html .= $this->input(Locale::t('JS date format'), 'datepicker_date_format', array('placeholder'=>'DD.MM.YYYY'));
        $html .= $this->checkbox(Locale::t('Optimization / Caching'), 'caching', $caching_attr, false);
        $html .= $this->input(Locale::t('Cache lifetime (sec.)'), 'cache_lifetime');
        $html .= $this->checkbox(Locale::t('Clean URLs'), 'clean_url', array('class'=>'form-control clean_url_option'), false);
        $html .= $this->checkbox(Locale::t('GZIP compression'), 'gzip', $gzip_attr, false);
        $html .= $this->checkbox(Locale::t('Hide file URLs'), 'hide_file_path', null, false);
        $html .= $this->checkbox(Locale::t('Enable widgets'), 'db_widgets_enabled', null, false);
        $html .= $this->checkbox(Locale::t('DB translates'), 'db_translates', null, false);
        if (count(Zira\Config::get('languages'))>1) {
            $html .= $this->checkbox(Locale::t('Detect language'), 'detect_language', null, false);
        }
        $html .= $this->checkbox(Locale::t('Sticky top bar'), 'dash_panel_frontend', null, false);
        $html .= $this->select(Locale::t('Window buttons position'), 'dashwindow_mode', array(
            '0' => Locale::t('Left'),
            '1' => Locale::t('Right')
        ));
        $html .= $this->checkbox(Locale::t('Maximize windows'), 'dashwindow_maximized', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerCustom(array(get_class(), 'checkTimezone'), 'timezone',Locale::t('Invalid value "%s"',Locale::t('Timezone')));
        $validator->registerString('watermark',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Watermark')));
        $validator->registerNumber('cache_lifetime',1,null,true,Locale::t('Invalid value "%s"',Locale::t('Cache lifetime (sec.)')));
        $validator->registerCustom(array(get_class(), 'checkWatermark'), 'watermark',Locale::t('Invalid value "%s"',Locale::t('Watermark')));
        $validator->registerCustom(array(get_class(), 'checkPHPDateFormat'), 'date_format',Locale::t('Invalid value "%s"',Locale::t('PHP date format')));
        $validator->registerCustom(array(get_class(), 'checkJSDateFormat'), 'datepicker_date_format',Locale::t('Invalid value "%s"',Locale::t('JS date format')));

        $watermark = $this->getValue('watermark');
        if (!empty($watermark)) {
            $watermark = trim($watermark,'/');
            $this->updateValues(array(
                'watermark' => $watermark
            ));
        }
    }

    public static function checkTimezone($timezone) {
        $timezone_list = timezone_identifiers_list();
        return in_array($timezone, $timezone_list);
    }

    public static function checkWatermark($watermark) {
        if (empty($watermark)) return true;
        if (strpos($watermark,'..')!==false) return false;

        $p = strrpos($watermark, '.');
        if ($p===false) return false;
        $ext = substr($watermark, $p+1);
        if (!in_array(strtolower($ext), array('jpg','jpeg','gif','png'))) return false;

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $watermark)) return false;

        $size = @getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $watermark);
        if (!$size) return false;

        return true;
    }

    public static function checkPHPDateFormat($date_format) {
        if (!preg_match('/^[a-zA-Z]([-.\x20][a-zA-Z]([-.\x20][a-zA-Z])?)?$/',$date_format)) return false;
        return true;
    }

    public static function checkJSDateFormat($datepicker_date_format) {
        if ($datepicker_date_format!='DD.MM.YYYY' && $datepicker_date_format!='MM.DD.YYYY') return false;
        return true;
    }
}