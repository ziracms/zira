<?php
/**
 * Zira project.
 * search.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Search extends Form
{
    protected $_id = 'fields-search-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

    protected $_checkbox_inline_label = true;
    
    protected static $_fields_array = null;
    protected $_fields = array();
    
    public function __construct()
    {
        parent::__construct($this->_id);
        $this->_token = 'field';
        $this->getValidator()->setToken($this->_token);
    }
    
    public function setFields(array $fields) {
        return $this->_fields = $fields;
    }
    
    public function getFields() {
        return $this->_fields;
    }

    public function getFieldsArray() {
        if (self::$_fields_array === null) {
            $cache_key = 'fields.search.'.Zira\Locale::getLanguage();
            $cached_fields = Zira\Cache::getArray($cache_key);
            if ($cached_fields!==false) {
                self::$_fields_array = $cached_fields;
            } else {
                self::$_fields_array = \Fields\Models\Field::loadRecordFields(array(), Zira\Locale::getLanguage(), false, true);
                Zira\Cache::setArray($cache_key, self::$_fields_array);
            }
        }
        return self::$_fields_array;
    }

    protected function _init()
    {
        $this->setUrl('fields/search');
        $this->setMethod(Zira\Request::GET);
        $this->setFill(Zira\Request::GET);
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal dash-window-form');
    }
    
    public function getNamePrefix() {
        return '';
    }

    protected function _render()
    {
        if (empty($this->_fields)) return ;
        $name_prefix = $this->getNamePrefix();
        $html = $this->open();
        foreach($this->_fields as $group_id=>$fields_group) {
            if (count($this->_fields)>1) {
                $group = $fields_group['group'];
                $label = Zira\Helper::tag('div', $group['title'].':');
                $html .= $this->wrap($this->wrap($label,'col-sm-12'));
            }
            foreach($fields_group['fields'] as $field) {
                $label = Locale::t($field->field_title);
                $name = $name_prefix.$field->field_id;
                if ($field->field_type == 'input' || $field->field_type == 'link' ||
                    $field->field_type == 'textarea' || $field->field_type == 'html' || 
                    $field->field_type == 'file' || $field->field_type == 'image'
                ) {
                    $html .= $this->input($label, $name);
                } else if ($field->field_type == 'checkbox') {
                    $attributes = array();
                    $attributes['class'] = $this->_input_class;
                    $attributes['style'] = 'width:auto;height:auto;outline:none';
                    $_field = Zira\Form\Form::checkbox($this->_token, $name, null, $attributes, $this->_fill);
                    $_html = Zira\Helper::tag_open('label', array('class'=>$this->_label_class, 'style'=>'width:auto;padding-top:0px'));
                    $_html .= $_field . $label;
                    $_html .= Zira\Helper::tag_close('label');
                    $elems = $this->wrap($_html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
                    $html .= $this->wrap($this->wrap($elems,$this->_input_offset_wrap_class));
                } else if ($field->field_type == 'radio') {
                    $value_titles = explode(',', $field->field_values);
                    $_value_titles = array();
                    foreach($value_titles as $value_title) {
                        $_value_titles[$value_title] = Locale::t($value_title);
                    }
                    $elems = '';
                    foreach ($_value_titles as $_value=>$_title) {
                        $attributes = array();
                        $_field = Zira\Form\Form::radio($this->_token, $name, $_value, $attributes, $this->_fill);
                        $_html = Zira\Helper::tag_open('label', array('style'=>'font-weight:normal'));
                        $_html .= $_field.' '.$_title;
                        $_html .= Zira\Helper::tag_close('label');
                        $elems .= $this->wrap($_html,'checkbox-block');
                    }
                    $label = Zira\Form\Form::label($label, null, array('class'=>$this->_label_class));
                    $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                } else if ($field->field_type == 'select') {
                    $value_titles = explode(',', $field->field_values);
                    $_value_titles = array(''=>Locale::tm('Please select','field'));
                    foreach($value_titles as $value_title) {
                        $_value_titles[$value_title] = Locale::t($value_title);
                    }
                    $html .= $this->select($label, $name, $_value_titles);
                } else if ($field->field_type == 'multiple') {
                    $options = explode(',', $field->field_values);
                    $elems = '';
                    $attributes = array();
                    $attributes['class'] = $this->_input_class;
                    $attributes['style'] = 'width:auto;height:auto;outline:none';
                    $co = 0;
                    foreach($options as $option) {
                        $_attributes = $attributes;
                        $id = 'field-item-multiple-option-'.$field->field_id.'-'.(++$co);
                        $_attributes['id'] = $id;
                        if (in_array($option, (array)Zira\Request::get($this->_token.'-'.$name))) $_attributes['checked'] = 'checked';
                        $_field = Zira\Form\Form::checkbox($this->_token, $name.'[]', $option, $_attributes, false);
                        $_html = Zira\Helper::tag_open('label', array('class'=>$this->_inline_label_class, 'style'=>'width:auto;padding-top:3px'));
                        $_html .= $_field . Locale::t($option);
                        $_html .= Zira\Helper::tag_close('label');
                        $elems .= $this->wrap($_html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
                    }
                    $label = Zira\Form\Form::label($label, $name, array('class'=>$this->_label_class));
                    $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                } 
            }
        }

        $label = Zira\Helper::tag('div', null, array('class'=>$this->_label_class));
        $elems = Zira\Helper::tag_open('button',array('type'=>'submit','class'=>'btn btn-default'));
        $elems .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-search'));
        $elems .= ' '.Locale::t('Search');
        $elems .= Zira\Helper::tag_close('button');
        $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
        
        $elems = Zira\Helper::tag_open('a', array('href'=>'javascript:void(0)', 'onclick'=>'$(this).parents(\'form\').get(0).reset();'));
        $elems .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-repeat'));
        $elems .= ' '.Locale::tm('Reset', 'fields');
        $elems .= Zira\Helper::tag_close('a');
        $html .= $this->wrap($this->wrap($elems,$this->_input_wrap_class));
        
        $html .= $this->close();
        
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->setMethod(Zira\Request::GET);

        $name_prefix = $this->getNamePrefix();
        foreach($this->_fields as $group_id=>$fields_group) {
            $group = $fields_group['group'];
            foreach ($fields_group['fields'] as $field) {
                $label = Locale::t($field->field_title);
                $name = $name_prefix.$field->field_id;
                if ($field->field_type == 'multiple') {
                    $validator->registerCustom(array(get_class(), 'checkMultiRadio'), array($name), Locale::t('Incorrect value "%s"',$label));
                }
                $validator->registerCustom(array(get_class(), 'validateString'), array($name), Locale::t('Incorrect value "%s"',$label));
            }
        }
    }
    
    public static function checkMultiRadio($multiple) {
        return empty($multiple) || is_array($multiple);
    }
    
    public static function validateString($inputs) {
        if (!is_array($inputs)) $inputs = array($inputs);
        foreach($inputs as $input) {
            if (strlen($input)>255) return false;
            if (strpos($input, '<')!==false) return false;
            if (strpos($input, '%')!==false) return false;
        }
        return true;
    }
}