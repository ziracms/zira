<?php
/**
 * Zira project.
 * field.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Field extends Form
{
    protected $_id = 'fields-item-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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
        $types = \Fields\Models\Field::getTypes();
        foreach($types as $var=>$val) {
            $types[$var] = Locale::tm($val, 'eform');
        }
        $html = $this->open();
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->input(Locale::t('Description'), 'description', array('placeholder'=>Locale::tm('visible for administrator', 'fields')));
        $html .= $this->select(Locale::tm('Type','fields').'*', 'field_type', $types, array('class'=>'form-control field-types-select'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'form_field_values_wrapper', 'style'=>'display:none'));
        $html .= $this->input(Locale::tm('Field values','fields').'*', 'form_field_values[]', array('class'=>'form-control field-values-input', 'id'=>''));
        $html .= Zira\Helper::tag_close('div');
        $html .= $this->checkbox(Locale::tm('add to record description','fields'), 'preview', null, false);
        $html .= $this->checkbox(Locale::tm('field is active','fields'), 'active', null, false);
        $html .= $this->hidden('id');
        $html .= $this->hidden('field_group_id');
        $html .= $this->hidden('field_values', array('class'=>'field-values-hidden'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerCustom(array(get_class(), 'checkGroup'), array('field_group_id'), Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkType'), array('field_type'), Locale::t('Invalid value "%s"',Locale::t('Type')));

        $validator->registerString('title',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title',Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title',Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerString('description',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerNoTags('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        
        $type = $this->getValue('field_type');
        if ($type == 'radio' || $type == 'select' || $type == 'multiple') {
            $validator->registerCustom(array(get_class(), 'checkValues'), 'form_field_values', Locale::t('Invalid value "%s"', Locale::t('Field values')));
        }
    }
    
    public static function checkGroup($group_id) {
        $group = new \Fields\Models\Group($group_id);
        return $group->loaded();
    }

    public static function checkType($type) {
        $types = \Fields\Models\Field::getTypes();
        return array_key_exists($type, $types);
    }
    
    public static function checkValues($values) {
        if (!is_array($values)) return false;
        $co = 0;
        foreach($values as $value) {
            if (strpos($value, '"')!==false) return false;
            if (strpos($value, ',')!==false) return false;
            if (preg_match('/[<][a-z\/][^>]*[>]/si', $value)) return false;
            if (Zira\Helper::utf8BadMatch($value)) return false;
            if (!empty($value)) $co++;
        }
        return $co>0;
    }
}