<?php
/**
 * Zira project
 * factory.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Form;

use Zira;
use Zira\Request;
use Zira\Helper;

class Factory {
    protected $_id;
    protected $_token;
    protected $_is_token_unique = false;
    protected $_url;
    protected $_method;
    protected $_validator;
    protected $_multipart = false;
    protected $_fill = Request::POST;
    protected $_title = '';
    protected $_description = '';
    protected $_render_panel = true;
    protected $_wrap_elements = true;
    protected $_values = array();
    protected $_message = '';
    protected $_error = '';
    protected $_info = '';
    protected $_ajax = false;

    // Bootstrap classes
    protected $_form_class = 'form-horizontal';
    protected $_group_class = 'form-group';
    protected $_input_group_class = 'input-group';
    protected $_input_group_addon_class = 'input-group-addon';
    protected $_input_group_button_class = 'input-group-btn';
    protected $_input_class = 'form-control';
    protected $_label_class = 'col-sm-3 control-label';
    protected $_input_wrap_class = 'col-sm-9';
    protected $_file_wrap_class = 'col-sm-4';
    protected $_input_offset_wrap_class = 'col-sm-offset-3 col-sm-9';
    protected $_checkbox_wrap_class = 'checkbox';
    protected $_checkbox_inline_wrap_class = 'checkbox-float';
    protected $_radio_wrap_class = 'radio-inline';
    protected $_button_group_class = 'btn-group';
    protected $_button_class = 'btn btn-default';
    protected $_submit_class = 'btn btn-primary';
    protected $_select_wrapper_class = 'col-sm-4';
    protected $_dropdown_class = 'form-dropdown dropdown';
    protected $_dropdown_menu_class = 'dropdown-menu';
    protected $_captcha_wrapper_class = 'col-sm-3';
    protected $_captcha_image_wrapper_class = 'captcha-image-wrapper';
    protected $_captcha_refresh_wrapper_class = 'input-group-addon';
    protected $_captcha_input_wrapper_class = 'input-group';
    protected $_captcha_refresh_ico_class = 'glyphicon glyphicon-refresh';
    protected $_panel_class = 'form-panel panel panel-default';
    protected $_panel_heading_class = 'panel-heading';
    protected $_panel_title_class = 'panel-title';
    protected $_panel_body_class = 'panel-body';
    protected $_panel_footer_class = 'panel-footer';
    protected $_alert_success_class = 'form-alert alert alert-success';
    protected $_alert_error_class = 'form-alert alert alert-danger';
    protected $_alert_info_class = 'form-alert alert alert-warning';
    protected $_ajax_form_class = 'xhr-form';
    protected $_help_class = 'help-block';
    protected $_field_error_class = 'form-field-error';
    protected $_icon_time_class = 'glyphicon glyphicon-time';
    protected $_date_wrap_class = 'date';

    protected $_checkbox_inline_label = true;
    protected $_checkbox_label_with_desc = false;

    public function __construct($id, $url=null, $method = Request::POST) {
        $this->_id = $id;
        $this->_url = $url;
        $this->_method = $method;
        $this->_token = Form::getToken($this->_id, $this->_is_token_unique);
        $this->_validator = new Validator();
        $this->_validator->setToken($this->_token);
        if ($this->_multipart) $this->_validator->setMultipart(true);
    }

    public function setValues(array $values) {
        $this->_values = $values;
    }

    public function setAjax($ajax) {
        $this->_ajax = (bool) $ajax;
    }

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function setMethod($method) {
        $this->_method = $method;
    }

    public function setMultipart($multipart) {
        $this->_multipart = (bool)$multipart;
        $this->_validator->setMultipart($multipart);
    }

    public function setFill($fill) {
        $this->_fill = (bool) $fill;
    }

    public function getToken() {
        return $this->_token;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function setMessage($message) {
        $this->_message = $message;
    }

    public function setError($error) {
        $this->_error = $error;
    }

    public function setInfo($info) {
        $this->_info = $info;
    }

    public function setDescription($description) {
        $this->_description = $description;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function getError() {
        return $this->_error;
    }

    public function getInfo() {
        return $this->_info;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setRenderPanel ($render_panel) {
        $this->_render_panel = $render_panel;
    }

    public function setWrapElements ($wrap_elements) {
        $this->_wrap_elements = $wrap_elements;
    }

    public function setFormClass($class) {
        $this->_form_class = $class;
    }

    public function setGroupClass($class) {
        $this->_group_class = $class;
    }

    public function setInputGroupClass($class) {
        $this->_input_group_class = $class;
    }

    public function setInputGroupAddonClass($class) {
        $this->_input_group_addon_class = $class;
    }

    public function setInputGroupButtonClass($class) {
        $this->_input_group_button_class = $class;
    }

    public function setInputClass($class) {
        $this->_input_class = $class;
    }

    public function setLabelClass($class) {
        $this->_label_class = $class;
    }

    public function setInputWrapClass($class) {
        $this->_input_wrap_class = $class;
    }

    public function setInputOffsetWrapClass($class) {
        $this->_input_offset_wrap_class = $class;
    }

    public function setCheckboxWrapClass($class) {
        $this->_checkbox_wrap_class = $class;
    }

    public function setRadioWrapClass($class) {
        $this->_radio_wrap_class = $class;
    }

    public function setButtonGroupClass($class) {
        $this->_button_group_class = $class;
    }

    public function setButtonClass($class) {
        $this->_button_class = $class;
    }

    public function setSubmitClass($class) {
        $this->_submit_class = $class;
    }

    public function setSelectWrapClass($class) {
        $this->_select_wrapper_class = $class;
    }

    public function setDropdownClass($class) {
        $this->_dropdown_class = $class;
    }

    public function setDropdownMenuClass($class) {
        $this->_dropdown_menu_class = $class;
    }

    public function setCaptchaWrapClass($class) {
        $this->_captcha_wrapper_class = $class;
    }

    public function setCaptchaImageWrapClass($class) {
        $this->_captcha_image_wrapper_class = $class;
    }

    public function setCaptchaRefreshWrapClass($class) {
        $this->_captcha_refresh_wrapper_class = $class;
    }

    public function setCaptchaInputWrapClass($class) {
        $this->_captcha_input_wrapper_class = $class;
    }

    public function setCaptchaRefreshIcoClass($class) {
        $this->_captcha_refresh_ico_class = $class;
    }

    public function setPanelClass($class) {
        $this->_panel_class = $class;
    }

    public function setPanelHeadingClass($class) {
        $this->_panel_heading_class = $class;
    }

    public function setPanelTitleClass($class) {
        $this->_panel_title_class = $class;
    }

    public function setPanelBodyClass($class) {
        $this->_panel_body_class = $class;
    }

    public function setPanelFooterClass($class) {
        $this->_panel_footer_class = $class;
    }

    public function setAlertSuccessClass($class) {
        $this->_alert_success_class = $class;
    }

    public function setAlertErrorClass($class) {
        $this->_alert_error_class = $class;
    }

    public function setAlertInfoClass($class) {
        $this->_alert_info_class = $class;
    }

    public function setAjaxFormClass($class) {
        $this->_ajax_form_class = $class;
    }

    public function getValidator() {
        return $this->_validator;
    }

    public function isErrorField($field) {
        $error_field = $this->getValidator()->getErrorField();
        if (!empty($error_field) && $error_field==$field) return true;
        return false;
    }

    public function getId() {
        return 'form-'.$this->_id;
    }

    public function getFieldName($name) {
        return Form::getFieldName($this->_token, $name);
    }

    public function open(array $attributes = null) {
        if (!$this->_url) $this->_url = Zira\Router::getRequest();

        if ($this->_ajax) {
            $this->_form_class .= ' '.$this->_ajax_form_class;

            if (strpos($this->_url,'?')!==false) $this->_url .= '&';
            else $this->_url .= '?';

            $this->_url .= FORMAT_GET_VAR.'='.FORMAT_JSON;
        }
        $prefix = '';
        $prefix .= $this->get_alerts();
        if ($this->_render_panel) {
            $prefix .= Helper::tag_open('div', array('class'=>$this->_panel_class));
            $prefix .= Helper::tag_open('div', array('class'=>$this->_panel_heading_class));
            if (!empty($this->_title)) {
                $prefix .= Helper::tag('h2', $this->_title, array('class'=>$this->_panel_title_class));
            }
            $prefix .= Helper::tag_close('div');
            $prefix .= Helper::tag_open('div', array('class'=>$this->_panel_body_class));
            if (!empty($this->_description)) {
                $prefix .= Helper::html($this->_description);
            }
            $prefix .= Helper::tag_close('div');
            $prefix .= Helper::tag_open('div', array('class'=>$this->_panel_footer_class));
        }

        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_form_class;
        $attributes['id'] = $this->getId();
        return $prefix.Form::open($this->_url,$this->_method, $this->_multipart, $attributes);
    }

    protected function get_alerts() {
        $html = '';
        if (!empty($this->_message)) {
            $html .= Helper::tag('div',$this->_message,array('class'=>$this->_alert_success_class));
        }
        if (!empty($this->_error)) {
            $html .= Helper::tag('div',$this->_error,array('class'=>$this->_alert_error_class));
        }
        if (!empty($this->_info)) {
            $html .= Helper::tag('div',$this->_info,array('class'=>$this->_alert_info_class));
        }
        return $html;
    }

    public function close() {
        $prefix = '';
        if ($this->_render_panel) {
            $prefix .= Helper::tag_close('div');
            $prefix .= Helper::tag_close('div');
        }
        return Form::close().$prefix;
    }

    public function wrap($element, $class = null) {
        if (!$this->_wrap_elements) return $element;
        if (!$class) $class = $this->_group_class;
        $html = Helper::tag_open('div',array('class'=>$class));
        $html .= $element;
        $html .= Helper::tag_close('div');
        return $html;
    }

    public function input($label, $name, array $attributes = null) {
        $value = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        if ($this->isErrorField($name)) $attributes['class'] .= ' '.$this->_field_error_class;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::input($this->_token, $name, $value, $attributes, $this->_fill);
        if (isset($attributes['title'])) {
            $field .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($field,$this->_input_wrap_class));
    }

    public function password($label, $name, array $attributes = null) {
        $value = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        if ($this->isErrorField($name)) $attributes['class'] .= ' '.$this->_field_error_class;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::password($this->_token, $name, $value, $attributes);
        if (isset($attributes['title'])) {
            $field .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($field,$this->_input_wrap_class));
    }

    public function hidden($name, array $attributes = null) {
        $value = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        return Form::hidden($this->_token, $name, $value, $attributes, $this->_fill);
    }

    public function token($value, array $attributes = null) {
        return Form::token($value, $attributes);
    }

    public function file($label, $name, array $attributes = null, $multiple = false) {
        if ($this->isErrorField($name)) {
            if ($attributes === null) $attributes = array();
            if (!isset($attributes['class']))
                $attributes['class'] = ' '.$this->_field_error_class;
            else
                $attributes['class'] .= ' '.$this->_field_error_class;
        }
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::file($this->_token, $name, $attributes, $multiple);
        if (isset($attributes['title'])) {
            $field .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($field,$this->_file_wrap_class));
    }

    public function fileButton($label, $name, array $attributes = null, $multiple = false, $text = null) {
        $_name = Form::getFieldName($this->_token, $name);
        if ($attributes === null) $attributes = array();
        $attributes['id'] = $_name;
        if ($text==null) $text = Zira\Locale::t('Browse');
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::file($this->_token, $name, $attributes, $multiple);
        $field = $this->wrap($text.$field,$this->_button_class.' form-file-button');
        $html = $this->wrap($text.$field,$this->_input_group_button_class);
        $html .= Helper::tag_short('input', array('type'=>'text','class'=>$this->_input_class,'id'=>$_name.'-text','readonly'=>'readonly'));
        $error_class = '';
        if ($this->isErrorField($name)) $error_class=' '.$this->_field_error_class;
        $elems = $this->wrap($html,$this->_input_group_class.$error_class);
        if (isset($attributes['title'])) {
            $elems .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($elems,$this->_file_wrap_class));
    }

    public function datepicker($label, $name, array $attributes = null) {
        $value = isset($this->_values[$name]) ? $this->_values[$name] : null;
        $id = $name.'-datepicker';
        if ($attributes === null) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        $error_class = '';
        if ($this->isErrorField($name)) $error_class=' '.$this->_field_error_class;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $html = Helper::tag_open('div', array('class'=>$this->_input_group_class.' '.$this->_date_wrap_class.$error_class,'id'=>$id));
        $html .= Form::input($this->_token, $name, $value, $attributes, $this->_fill);
        $html .= Helper::tag_open('span', array('class'=>$this->_input_group_addon_class));
        $html .= Helper::tag('span', null, array('class'=>$this->_icon_time_class));
        $html .= Helper::tag_close('span');
        $html .= Helper::tag_close('div');
        if (isset($attributes['title'])) {
            $html .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($html,$this->_input_wrap_class));
    }

    /**
     * @param $name
     * @param string $viewMode - accepts 'decades','years','months','days'
     * @param null $maxDate - format 'Y-m-d'
     */
    public function initDatepicker($name, $viewMode = null, $maxDate = null) {
        Zira\View::addDatepicker($viewMode, $maxDate);
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= "jQuery(document).ready(function(){";
        $script .= "zira_datepicker(jQuery('#".$name."'))";
        $script .= "});";
        $script .= Helper::tag_close('script');
        Zira\View::addBodyBottomScript ($script);
    }

    public function parseDatepickerDate($value) {
        $date = '';
        $format = Zira\Config::get('datepicker_date_format');
        if (preg_match_all('/(?:DD|MM|YYYY)/', $format, $m) && !empty($m[0])) {
            $days = -1; $months = -1; $years = -1;
            foreach($m[0] as $i=>$_m) {
                if ($_m == 'DD') $days=$i;
                if ($_m == 'MM') $months=$i;
                if ($_m == 'YYYY') $years=$i;
            }
            if (preg_match_all('/[\d]+/', $value, $m1) && !empty($m1[0])) {
                $day = ''; $month = ''; $year = '';
                foreach($m1[0] as $i=>$_m1) {
                    if ($i == $days) $day .= $_m1;
                    else if ($i == $months) $month .= $_m1;
                    else if ($i == $years) $year .= $_m1;
                }
                if (!empty($day) && !empty($month) && !empty($year)) {
                    $date=date('Y-m-d',mktime(0,0,0,intval($month),intval($day),intval($year)));
                }
            }
        }
        return $date;
    }

    public function prepareDatepickerDate($value) {
        if (empty($value)) return $value;
        $time = strtotime($value);
        $format = Zira\Config::get('datepicker_date_format');
        $format = str_replace('DD','%1$s',$format);
        $format = str_replace('MM','%2$s',$format);
        $format = str_replace('YYYY','%3$s',$format);
        $day = date('d', $time);
        $month = date('m', $time);
        $year = date('Y', $time);
        return sprintf($format,$day,$month,$year);
    }

    public function checkbox($label, $name, array $attributes = null, $fill=true) {
        $checked = !empty($this->_values[$name]);
        if (!$attributes) $attributes = array();
        if ($checked) $attributes['checked'] = 'checked';

        if ($this->_checkbox_inline_label && !$this->_checkbox_label_with_desc) {
            if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
            if (!isset($attributes['style'])) $attributes['style'] = 'width:auto;height:auto;outline:none';
            $field = Form::checkbox($this->_token, $name, null, $attributes, ($fill ? $this->_fill : false));
            $html = Helper::tag_open('label', array('for' => $name, 'class'=>$this->_label_class, 'style'=>'width:auto;padding-top:0px'));
            $html .= $field . $label;
            $html .= Helper::tag_close('label');
            $elems = $this->wrap($html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
            return $this->wrap($this->wrap($elems,$this->_input_offset_wrap_class));
        } else if (!$this->_checkbox_label_with_desc) {
            $label = Form::label($label, $name, array('class'=>$this->_label_class));
            if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
            if (!isset($attributes['style'])) $attributes['style'] = 'width:auto;outline:none';
            $field = Form::checkbox($this->_token, $name, null, $attributes, ($fill ? $this->_fill : false));
            return $this->wrap($label.$this->wrap($field,$this->_input_wrap_class));
        } else {
            $desc = isset($attributes['title']) ? $attributes['title'] : '';
            $label = Form::label($label, $name, array('class'=>$this->_label_class));
            if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
            if (!isset($attributes['style'])) $attributes['style'] = 'width:auto;height:auto;outline:none';
            $field = Form::checkbox($this->_token, $name, null, $attributes, ($fill ? $this->_fill : false));
            $html = Helper::tag_open('label', array('for' => $name, 'class'=>$this->_label_class, 'style'=>'width:auto;padding-top:3px'));
            $html .= $field . $desc;
            $html .= Helper::tag_close('label');
            $elems = $this->wrap($html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
            return $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
        }
    }

    public function checkboxButton($label, $name, array $attributes = null, $fill=true) {
        $checked = !empty($this->_values[$name]);
        if (!$attributes) $attributes = array();
        if ($checked) $attributes['checked'] = 'checked';
        $field = Form::checkbox($this->_token, $name, null, $attributes, ($fill ? $this->_fill : false));
        $active = strpos($field,' checked="checked"') ? ' active' : '';
        $html = Helper::tag_open('div',array('class'=>$this->_button_group_class,'data-toggle'=>'buttons'));
        $html .= Helper::tag_open('label',array('class'=>$this->_button_class.$active));
        $html .= $field.$label;
        $html .= Helper::tag_close('label');
        $html .= Helper::tag_close('div');
        if (isset($attributes['title'])) {
            $html .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($this->wrap($html,$this->_input_offset_wrap_class));
    }

    public function radio($label, $name, array $value_titles, array $attributes = null, $fill=true) {
        $checked = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        $elems = '';
        foreach ($value_titles as $value=>$title) {
            $_attributes = array();
            if ($checked!==null && $checked==$value) {
                $_attributes['checked'] = 'checked';
            }
            $field = Form::radio($this->_token, $name, $value, array_merge($attributes,$_attributes), ($fill ? $this->_fill : false));
            $html = Helper::tag_open('label');
            $html .= $field.$title;
            $html .= Helper::tag_close('label');
            $elems .= $this->wrap($html,$this->_radio_wrap_class);
        }
        if (isset($attributes['title'])) {
            $elems .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        $label = Form::label($label, null, array('class'=>$this->_label_class));
        return $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
    }

    public function radioButton($label, $name, array $value_titles, array $attributes = null, $fill=true) {
        $checked = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        $elems = Helper::tag_open('div',array('class'=>$this->_button_group_class,'data-toggle'=>'buttons'));
        foreach ($value_titles as $value=>$title) {
            $_attributes = array();
            if ($checked!==null && $checked==$value) {
                $_attributes['checked'] = 'checked';
            }
            $field = Form::radio($this->_token, $name, $value, array_merge($attributes,$_attributes), ($fill ? $this->_fill : false));
            $active = strpos($field,' checked="checked"') ? ' active' : '';

            $html = Helper::tag_open('label',array('class'=>$this->_button_class.$active));
            $html .= $field.$title;
            $html .= Helper::tag_close('label');
            $elems .= $html;
        }
        $elems .= Helper::tag_close('div');
        if (isset($attributes['title'])) {
            $elems .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        $label = Form::label($label, null, array('class'=>$this->_label_class));
        return $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
    }

    public function textarea($label, $name, array $attributes = null) {
        $value = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        if ($this->isErrorField($name)) $attributes['class'] .= ' '.$this->_field_error_class;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::textarea($this->_token, $name, $value, $attributes, $this->_fill);
        if (isset($attributes['title'])) {
            $field .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($field,$this->_input_wrap_class));
    }

    public function select($label, $name, array $options=null, array $attributes = null) {
        $selected = isset($this->_values[$name]) ? $this->_values[$name] : null;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_input_class;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));
        $field = Form::select($this->_token, $name, $options, $selected, $attributes, $this->_fill);
        if (isset($attributes['title'])) {
            $field .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($field,$this->_select_wrapper_class));
    }

    public function selectDropdown($label, $name, array $options=null, array $attributes = null) {
        $_name = Form::getFieldName($this->_token, $name);
        $selected = isset($this->_values[$name]) ? $this->_values[$name] : null;
        $label = Form::label($label, $name, array('class'=>$this->_label_class));

        if ($this->_fill) {
            if ($this->_fill == Request::POST && Request::isPost()) $_value = Request::post($_name);
            else if ($this->_fill == Request::GET) $_value = Request::get($_name);
        }

        if (isset($_value)) $selected = $_value;
        if ($selected===null && $options!==null && count($options)>0) {
            $selected = array_keys($options)[0];
        }

        $field = Form::hidden($this->_token, $name, $selected, $attributes, $this->_fill);

        $button_id = $_name.'-dropdown-label';
        if (!empty($options)) {
            $button_label = $selected!==null && isset($options[$selected]) ? $options[$selected] : reset($options);
        } else {
            $button_label = '';
        }
        $dropdown = Helper::tag_open('div',array('class'=>$this->_dropdown_class));
        $dropdown .= Helper::tag_open('button',array('class'=>$this->_button_class,'id'=>$button_id,'type'=>'button','data-toggle'=>'dropdown','aria-haspopup'=>'true','aria-expanded'=>'false'));
        $dropdown .= Helper::html($button_label.' ');
        $dropdown .= Helper::tag('span','',array('class'=>'caret'));
        $dropdown .= Helper::tag_close('button');
        $dropdown .= Helper::tag_open('ul',array('class'=>$this->_dropdown_menu_class,'aria-labelledby'=>$button_id,'rel'=>$_name));
        foreach ($options as $option_value=>$option_name) {
            $dropdown .= Helper::tag_open('li');
            $dropdown .= Helper::tag('a',$option_name,array('href'=>'javascript:void(0)','rel'=>$option_value));
            $dropdown .= Helper::tag_close('li');
        }
        $dropdown .= Helper::tag_close('ul');
        $dropdown .= Helper::tag_close('div');
        if (isset($attributes['title'])) {
            $dropdown .= Helper::tag('p', $attributes['title'], array('class'=>$this->_help_class));
        }

        return $this->wrap($label.$this->wrap($field.$dropdown,$this->_input_wrap_class));
    }

    public function button($label, array $attributes = null) {
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_button_class;
        $field = Form::button($label,$attributes);
        return $this->wrap($this->wrap($field,$this->_input_offset_wrap_class));
    }

    public function submit($label, array $attributes = null) {
        if (!$attributes) $attributes = array();
        if (!isset($attributes['class'])) $attributes['class'] = $this->_submit_class;
        $field = Form::submit($label,$attributes);
        return $this->wrap($this->wrap($field,$this->_input_offset_wrap_class));
    }

    public function captcha($label, $description = null) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return '';
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA) return $this->_captcha_recaptcha();
        else return $this->_captcha_default($label, $description);
    }

    public function captchaLazy($label, $description = null) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return '';
        if (!Zira\Models\Captcha::isActive($this->_id)) return '';
        return $this->captcha($label, $description);
    }

    protected function _captcha_default($label, $description = null) {
        $error_class = '';
        if ($this->isErrorField(CAPTCHA_NAME)) $error_class=' '.$this->_field_error_class;
        $label = Form::label($label, null, array('class'=>$this->_label_class));
        $captcha = Form::captcha(
            $this->_token,
            $this->_captcha_image_wrapper_class,
            $this->_captcha_input_wrapper_class.$error_class,
            $this->_input_class,
            $this->_captcha_refresh_wrapper_class,
            Helper::tag('span',null,array('class'=>$this->_captcha_refresh_ico_class))
        );
        if (isset($description)) {
            $captcha .= Helper::tag('p', $description, array('class'=>$this->_help_class));
        }
        return $this->wrap($label.$this->wrap($captcha,$this->_captcha_wrapper_class));
    }

    protected function _captcha_recaptcha() {
        $label = Form::label(' ', null, array('class'=>$this->_label_class));
        $captcha = Form::recaptcha(
            Zira\Config::get('recaptcha_site_key','')
        );
        return $this->wrap($label.$this->wrap($captcha,$this->_input_wrap_class));
    }

    public function validate() {
        if (!$this->getValidator()->validate()) {
            $this->setError($this->getValidator()->getMessage());
            return false;
        }
        return true;
    }
}