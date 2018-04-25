<?php
/**
 * Zira project.
 * value.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Value extends Form
{
    protected $_id = 'fields-values-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

    protected $_checkbox_inline_label = true;
    
    protected $_fields = array();
    protected $_field_values = array();
    
    const THUMBS_HEIGHT = 100; // img tag height

    public function __construct()
    {
        parent::__construct($this->_id);
    }
    
    public function loadFields($record) {
        $category_ids = array(Zira\Category::ROOT_CATEGORY_ID);
        if ($record->category_id != Zira\Category::ROOT_CATEGORY_ID) {
            $category_ids []= $record->category_id;
            $category = new Zira\Models\Category($record->category_id);
            if ($category->loaded()) {
                $chain = explode('/', $category->name);
                if (count($chain)>1) {
                    $names = array();
                    do {
                        array_pop($chain);
                        $names []= implode('/', $chain);
                    } while(count($chain)>0);
                    $rows = Zira\Models\Category::getCollection()
                                    ->select('id')
                                    ->where('name', 'in', $names)
                                    ->get();
                    foreach($rows as $row) {
                        $category_ids []= $row->id;
                    }
                }
            }
        }
        $this->_fields = \Fields\Models\Field::loadRecordFields($category_ids, $record->language);
    }
    
    public function loadFieldValues($record) {
        $this->_field_values = \Fields\Models\Value::loadRecordValues($record->id);
    }
    
    public function getFields() {
        return $this->_fields;
    }
    
    public function getFieldValues() {
        return $this->_field_values;
    }

    protected function _init()
    {
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal dash-window-form');
    }
    
    public function getNamePrefix() {
        return 'fields-item-';
    }

    protected function _render()
    {
        if (empty($this->_fields)) {
            return Zira\Helper::tag('div', Locale::tm('No extra fields found for this record.', 'fields'), array('class'=>'alert alert-warning')).
                    Zira\Helper::tag_open('div', array('style'=>'text-align:center;padding-top:40px')).
                    Zira\Helper::tag('button', Locale::tm('Create new extra fields', 'fields'), array('class'=>'btn btn-default fields_new_group_btn')).
                    Zira\Helper::tag_close('div');
        }
        $name_prefix = $this->getNamePrefix();
        $html = $this->open();
        $html .= Zira\Helper::tag_open('div', array('class'=>'record_fields_tab_wrapper'));
        $html .= Zira\Helper::tag_open('ul', array('class'=>'nav nav-tabs', 'role'=>'tablist'));
        $co=0;
        foreach ($this->_fields as $group_id=>$fields_group) {
            $group = $fields_group['group'];
            $class = $co === 0 ? 'active' : '';
            $html .= Zira\Helper::tag_open('li', array('role'=>'presentation', 'class'=>$class));
            $html .= Zira\Helper::tag('a', Locale::t($group['title']), array('href'=>'#field-group-'.$group['id'], 'aria-control'=>'field-group-'.$group['id'], 'role'=>'tab', 'data-toggle'=>'tab'));
            $html .= Zira\Helper::tag_close('li');
            $co++;
        }
        $html .= Zira\Helper::tag_close('ul');

        $html .= Zira\Helper::tag_open('div', array('class'=>'tab-content'));
        $co=0;
        foreach ($this->_fields as $group_id=>$fields_group) {
            $group = $fields_group['group'];
            $class = $co === 0 ? 'tab-pane active' : 'tab-pane';
            $html .= Zira\Helper::tag_open('div', array('role'=>'tab-panel', 'id'=>'field-group-'.$group['id'], 'class'=>$class));
            $html .= Zira\Helper::tag_open('div', array('style'=>'padding:10px;'));
            if (!empty($group['description'])) {
                $html .= Zira\Helper::tag('div', Locale::t($group['description']), array('class'=>'alert alert-info'));
            }
            if (array_key_exists($group['id'], $this->_fields)) {
                foreach($fields_group['fields'] as $field) {
                    $label = Locale::t($field->field_title);
                    $field->field_description = Locale::t($field->field_description);
                    $name = $name_prefix.$field->field_id;
                    $value = null;
                    $value_date_added = '';
                    if (array_key_exists($field->field_id, $this->_field_values)) {
                        $value = $this->_field_values[$field->field_id]->content;
                        $value_date_added = $this->_field_values[$field->field_id]->date_added;
                        if ($field->field_type != 'multiple' &&
                            $field->field_type != 'file' &&
                            $field->field_type != 'image'
                        ) {
                            $this->_values[$name] = $value;
                        }
                    }
                    if ($field->field_type == 'input' || $field->field_type == 'link') {
                        $html .= $this->input($label, $name, array('title'=>$field->field_description));
                    } else if ($field->field_type == 'checkbox') {
                        $attributes = array();
                        $attributes['class'] = $this->_input_class;
                        $attributes['style'] = 'width:auto;height:auto;outline:none';
                        if ($value) $attributes['checked'] = 'checked';
                        $_field = Zira\Form\Form::checkbox($this->_token, $name, null, $attributes, false);
                        $_html = Zira\Helper::tag_open('label', array('for' => $name, 'class'=>$this->_label_class, 'style'=>'width:auto;padding-top:0px'));
                        $_html .= $_field . $label;
                        $_html .= Zira\Helper::tag_close('label');
                        $elems = $this->wrap($_html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
                        if ($field->field_description) {
                            $elems .= Zira\Helper::tag('p', $field->field_description, array('class'=>'help-block'));
                        }
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
                            if ($value!==null && $value==$_value) {
                                $attributes['checked'] = 'checked';
                            }
                            $_field = Zira\Form\Form::radio($this->_token, $name, $_value, $attributes, false);
                            $_html = Zira\Helper::tag_open('label', array('style'=>'font-weight:normal'));
                            $_html .= $_field.' '.$_title;
                            $_html .= Zira\Helper::tag_close('label');
                            $elems .= $this->wrap($_html,'checkbox-block');
                        }
                        if ($field->field_description) {
                            $elems .= Zira\Helper::tag('p', $field->field_description, array('class'=>$this->_help_class));
                        }
                        $label = Zira\Form\Form::label($label, null, array('class'=>$this->_label_class));
                        $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                    } else if ($field->field_type == 'textarea' || $field->field_type == 'html') {
                        $html .= $this->textarea($label, $name, array('title'=>$field->field_description));
                    } else if ($field->field_type == 'select') {
                        $value_titles = explode(',', $field->field_values);
                        $_value_titles = array(''=>Locale::tm('Please select','field'));
                        foreach($value_titles as $value_title) {
                            $_value_titles[$value_title] = Locale::t($value_title);
                        }
                        $html .= $this->select($label, $name, $_value_titles, array('title'=>$field->field_description));
                    } else if ($field->field_type == 'multiple') {
                        $options = explode(',', $field->field_values);
                        $elems = '';
                        $attributes = array();
                        $attributes['class'] = $this->_input_class;
                        $attributes['style'] = 'width:auto;height:auto;outline:none';
                        $co = 0;
                        $_value = array();
                        if (!empty($value)) {
                            $_value = explode(',',$value);
                        }
                        foreach($options as $option) {
                            $_attributes = $attributes;
                            $id = 'field-item-multiple-option-'.$field->field_id.'-'.(++$co);
                            $_attributes['id'] = $id;
                            if (in_array($option, $_value)) $_attributes['checked'] = 'checked';
                            $_field = Zira\Form\Form::checkbox($this->_token, $name.'[]', $option, $_attributes, false);
                            $_html = Zira\Helper::tag_open('label', array('for' => $id, 'class'=>$this->_inline_label_class, 'style'=>'width:auto;padding-top:3px'));
                            $_html .= $_field . Locale::t($option);
                            $_html .= Zira\Helper::tag_close('label');
                            $elems .= $this->wrap($_html,$this->_checkbox_wrap_class.' '.$this->_checkbox_inline_wrap_class);
                        }
                        $label = Zira\Form\Form::label($label, $name, array('class'=>$this->_label_class));
                        if ($field->field_description) {
                            $elems .= Zira\Helper::tag('p', $field->field_description, array('class'=>'help-block'));
                        }
                        $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                    } else if ($field->field_type == 'file') {
                        $_value = array();
                        if (!empty($value)) {
                            $_value = explode(',',$value);
                        }
                        $label = Zira\Form\Form::label($label, $name, array('class'=>$this->_label_class));
                        $elems = Zira\Helper::tag_open('div', array('class'=>'fields_record_files_wrapper'));
                        foreach($_value as $_val) {
                            $_val_title = rawurldecode(ltrim(substr($_val, (int)strrpos($_val, '/')),'/'));
                            $elems .= '<div style="margin-bottom:4px"><span class="glyphicon glyphicon-remove-circle fields_record_files_hidden_remove" style="cursor:pointer"></span> <a href="'.Zira\Helper::baseUrl($_val).'" data-url="'.$_val.'" target="_blank">'.$_val_title.'</a></div>';
                        }
                        $elems .= Zira\Helper::tag_close('div');
                        $elems .= Zira\Helper::tag('button', Locale::tm('Add files', 'fields'), array('class'=>'btn btn-default fields_record_file_btn'));
                        foreach($_value as $_val) {
                            $elems .= $this->hidden($name.'[]', array('id'=>'', 'class'=>'fields_record_files_hidden', 'value'=>$_val));
                        }
                        $elems .= $this->hidden($name.'[]', array('id'=>'', 'class'=>'fields_record_files_hidden'));
                        if ($field->field_description) {
                            $elems .= Zira\Helper::tag('p', $field->field_description, array('class'=>'help-block'));
                        }
                        $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                    } else if ($field->field_type == 'image') {
                        $_value = array();
                        if (!empty($value)) {
                            $_value = explode(',',$value);
                        }
                        $label = Zira\Form\Form::label($label, $name, array('class'=>$this->_label_class));
                        $elems = Zira\Helper::tag_open('div', array('class'=>'fields_record_images_wrapper'));
                        foreach($_value as $_val) {
                            $_val_title = rawurldecode(ltrim(substr($_val, (int)strrpos($_val, '/')),'/'));
                            $elems .= '<div style="position:relative;margin-bottom:4px;display:inline-block;vertical-align:top;margin:0px 2px 2px 0px;"><span class="glyphicon glyphicon-remove-circle fields_record_images_hidden_remove" style="position:absolute;cursor:pointer;z-index:9;background:white;"></span> <img src="'.Zira\Helper::baseUrl(\Fields\Models\Value::getImageThumb($_val)).'?t='.\Fields\Models\Value::getThumbTag($value_date_added).'" height="'.self::THUMBS_HEIGHT.'" data-url="'.$_val.'" alt="'.Zira\Helper::html($_val_title).'" /></div>';
                        }
                        $elems .= Zira\Helper::tag_close('div');
                        $elems .= Zira\Helper::tag('button', Locale::tm('Add images', 'fields'), array('class'=>'btn btn-default fields_record_image_btn'));
                        foreach($_value as $_val) {
                            $elems .= $this->hidden($name.'[]', array('id'=>'', 'class'=>'fields_record_images_hidden', 'value'=>$_val));
                        }
                        $elems .= $this->hidden($name.'[]', array('id'=>'', 'class'=>'fields_record_images_hidden'));
                        if ($field->field_description) {
                            $elems .= Zira\Helper::tag('p', $field->field_description, array('class'=>'help-block'));
                        }
                        $html .= $this->wrap($label.$this->wrap($elems,$this->_input_wrap_class));
                    } 
                }
            }
            $html .= Zira\Helper::tag_close('div');
            $html .= Zira\Helper::tag_close('div');
            $co++;
        }
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        
        $html .= $this->hidden('record_id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $name_prefix = $this->getNamePrefix();
        foreach ($this->_fields as $group_id=>$fields_group) {
            $group = $fields_group['group'];
            foreach ($fields_group['fields'] as $field) {
                $label = Locale::t($field->field_title);
                $name = $name_prefix.$field->field_id;
                $required = false;
                if ($field->field_type == 'input') {
                    $validator->registerString($name, null, 255, $required, Locale::t('Invalid value "%s"',$label));
                    $validator->registerNoTags($name, Locale::t('Invalid value "%s"',$label));
                } else if ($field->field_type == 'textarea') {
                    $validator->registerText($name, null, $required, Locale::t('Invalid value "%s"',$label));
                    $validator->registerNoTags($name, Locale::t('Invalid value "%s"',$label));
                } else if ($field->field_type == 'multiple') {
                    $validator->registerCustom(array(get_class(), 'checkMultiRadio'), array($name), Locale::t('Invalid value "%s"',$label));
                } else if ($field->field_type == 'link') {
                    $validator->registerString($name, null, 255, $required, Locale::t('Invalid value "%s"',$label));
                    $validator->registerNoTags($name, Locale::t('Invalid value "%s"',$label));
                    $validator->registerCustom(array(get_class(), 'checkLink'), array($name), Locale::t('Invalid value "%s"',$label));
                } else if ($field->field_type == 'file' || $field->field_type == 'image') {
                    $validator->registerCustom(array(get_class(), 'checkFile'), array($name.'[]'), Locale::t('Invalid value "%s"',$label));
                } 
            }
        }
    }
    
    public static function checkMultiRadio($multiple) {
        return empty($multiple) || is_array($multiple);
    }
    
    public static function checkLink($link) {
        if (empty($link)) return true;
        if (strpos($link, 'http://')!==0 && strpos($link, 'https://')!==0) return false;
        return true;
    }
    
    public static function checkFile($files) {
        return empty($files) || is_array($files);
    }
}