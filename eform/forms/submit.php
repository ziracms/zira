<?php
/**
 * Zira project.
 * submit.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Submit extends Form
{
    const FILE_MAX_SIZE = 2097152; // 2Mb
    protected $allowed_file_extensions = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF', 'bmp', 'BMP', 'txt', 'TXT', 'doc', 'DOC', 'docx', 'DOCX', 'pdf', 'PDF', 'ppt', 'PPT', 'pptx', 'PPTX', 'xls', 'XLS', 'xlsx', 'XLSX', 'xml', 'XML', 'psd', 'PSD', 'rtf', 'RTF', 'zip', 'ZIP');

    protected $_id = 'eform-submit-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

    protected $_checkbox_label_with_desc = true;

    protected $_eform;
    protected $_fields;
    protected $_has_required = false;
    protected $_has_file = false;

    public function __construct($eform, $fields, $has_required, $has_file, $ajax = false)
    {
        $this->_eform = $eform;
        $this->_fields = $fields;
        $this->_has_required = $has_required;
        $this->_has_file = $has_file;
        if ($ajax) $this->setAjax(true);
        parent::__construct($this->_id.'-'.$this->_eform->name);
    }

    protected function _init()
    {
        $this->setRenderPanel(true);
        $this->setTitle(Locale::t($this->_eform->title));
        if ($this->_has_required) {
            $this->setDescription(Locale::tm('Fields marked with an asterisk are required', 'eform'));
        } else {
            $this->setDescription(Locale::tm('Please fill out form fields', 'eform'));
        }
        if ($this->_has_file) {
            $this->setMultipart(true);
        }
        
        $name_prefix = $this->getNamePrefix();
        foreach($this->_fields as $field) {
            $name = $name_prefix.$field->id;
            if ($field->field_type == 'datepicker') {
                $this->initDatepicker($name);
            }
        }
    }

    public function getNamePrefix() {
        return 'eform-field-'.$this->_eform->id.'-';
    }

    protected function _render()
    {
        $name_prefix = $this->getNamePrefix();
        $html = $this->open();
        foreach($this->_fields as $field) {
            $label = Locale::t($field->label);
            $name = $name_prefix.$field->id;
            if ($field->required) $label .= '*';
            if ($field->field_type == 'email' || $field->field_type == 'input') {
                $html .= $this->input($label, $name, array('title'=>$field->description));
            } else if ($field->field_type == 'file') {
                $html .= $this->fileButton($label, $name, array('title'=>$field->description));
            } else if ($field->field_type == 'datepicker') {
                $html .= $this->datepicker($label, $name, array('title'=>$field->description));
            } else if ($field->field_type == 'checkbox') {
                $html .= $this->checkbox($label, $name, array('title'=>$field->description));
            } else if ($field->field_type == 'radio') {
                $value_titles = explode(',', $field->field_values);
                $html .= $this->radioButton($label, $name, $value_titles, array('title'=>$field->description));
            } else if ($field->field_type == 'textarea') {
                $html .= $this->textarea($label, $name, array('title'=>$field->description));
            } else if ($field->field_type == 'select') {
                $options = explode(',', Locale::tm('Please select','eform').','.$field->field_values);
                $html .= $this->selectDropdown($label, $name, $options, array('title'=>$field->description));
            }
        }
        $html .= $this->captcha(Locale::t('Anti-Bot').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));

        $name_prefix = $this->getNamePrefix();
        foreach($this->_fields as $field) {
            $label = Locale::t($field->label);
            $name = $name_prefix.$field->id;
            $required = $field->required;
            if ($field->field_type == 'email') {
                $validator->registerEmail($name, $required, Locale::tm('Invalid email address', 'eform'));
            } else if ($field->field_type == 'input') {
                $validator->registerString($name, null, 255, $required, Locale::tm('Invalid value for "%s"', 'eform', $label));
                $validator->registerNoTags($name, Locale::tm('Field "%s" contains invalid character', 'eform', $label));
            } else if ($field->field_type == 'file') {
                $validator->registerFile($name, self::FILE_MAX_SIZE, $this->allowed_file_extensions, $required, Locale::tm('Unsupported file uploaded', 'eform'));
            } else if ($field->field_type == 'datepicker') {
                $validator->registerDate($name, $required, Locale::tm('Invalid date', 'eform'));
            } else if ($field->field_type == 'checkbox') {
                $validator->registerString($name, null, null, $required, Locale::tm('You should tick on "%s"', 'eform', $label));
            } else if ($field->field_type == 'radio') {
                $value_titles = explode(',', $field->field_values);
                $validator->registerNumber($name, 0, count($value_titles)-1, $required, Locale::tm('You should select "%s"', 'eform', $label));
            } else if ($field->field_type == 'textarea') {
                $validator->registerText($name, null, $required, Locale::tm('Invalid value for "%s"', 'eform', $label));
                $validator->registerNoTags($name, Locale::tm('Field "%s" contains invalid character', 'eform', $label));
            } else if ($field->field_type == 'select') {
                $options = explode(',', $field->field_values);
                $validator->registerNumber($name, $required ? 1 : 0, count($options), $required, Locale::tm('You should select "%s"', 'eform', $label));
            }
        }
    }
}