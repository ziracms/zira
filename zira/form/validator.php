<?php
/**
 * Zira project
 * validator.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Form;

use Zira;
use Zira\Request;

class Validator {
    protected $_token;
    protected $_method = Request::POST;
    protected $_multipart = false;
    protected $_fields = array();
    protected $_message = '';
    protected $_error_field = '';
    protected $_form_id = '';

    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_EMAIL = 'email';
    const TYPE_TEXT = 'text';
    const TYPE_PHONE = 'phone';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_CAPTCHA = 'captcha';
    const TYPE_CAPTCHA_LAZY = 'captcha_lazy';
    const TYPE_MATCH = 'match';
    const TYPE_REGEXP = 'regexp';
    const TYPE_EXISTS = 'exists';
    const TYPE_NO_TAGS = 'no_tags';
    const TYPE_UTF8 = 'utf8';
    const TYPE_CUSTOM = 'custom';

    public function setToken($token) {
        $this->_token = $token;
    }

    public function getToken() {
        return $this->_token;
    }
    
    public function setFormId($id) {
        $this->_form_id = $id;
    }

    public function getFormId() {
        return $this->_form_id;
    }

    public function setMethod($method) {
        $this->_method = $method;
    }

    public function setMultipart($multipart) {
        $this->_multipart = (bool)$multipart;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function getErrorField() {
        return $this->_error_field;
    }

    protected function getValue(array $field, $method = null) {
        if (!$method) $method = $this->_method;
        $value = Form::getValue($field['token'],$field['name'],$method);
        if ($value===null && $method == Request::POST && $this->_multipart) {
            $value = Form::getValue($field['token'],$field['name'],Request::FILES);
        }
        return $value;
    }

    public function registerString($field,$min_length,$max_length,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_STRING,
            'token' => $this->_token,
            'name' => $field,
            'min' => $min_length,
            'max' => $max_length,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateString(array $field) {
        $value = $this->getValue($field);
        $value = trim($value);
        if ($field['required'] && empty($value)) return false;
        if (!empty($value) && !is_string($value)) return false;
        if (!empty($value) && $field['min']>0 && mb_strlen($value,CHARSET)<$field['min']) return false;
        if (!empty($value) && $field['max']>0 && mb_strlen($value,CHARSET)>$field['max']) return false;
        return true;
    }

    public function registerNoTags($field,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_NO_TAGS,
            'token' => $this->_token,
            'name' => $field,
            'message' => $message
        );
    }

    protected function validateNoTags(array $field) {
        $value = $this->getValue($field);
        if (empty($value)) return true;
//        if (strpos($value, '<')!==false || strpos($value, '>')!==false) return false;
//        return true;
        return !preg_match('/[<][a-z\/][^>]*[>]/si', $value);
    }

    public function registerUtf8($field,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_UTF8,
            'token' => $this->_token,
            'name' => $field,
            'message' => $message
        );
    }

    protected function validateUtf8(array $field) {
        $value = $this->getValue($field);
        if (empty($value)) return true;
        return !Zira\Helper::utf8BadMatch($value);
    }

    public function registerNumber($field,$min_value,$max_value,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_NUMBER,
            'token' => $this->_token,
            'name' => $field,
            'min' => $min_value,
            'max' => $max_value,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateNumber(array $field) {
        $value = $this->getValue($field);
        if ($field['required'] && $value===null) return false;
        if (!empty($value) && !is_numeric($value)) return false;
        if (!empty($value) && $field['min']!==null && $value<$field['min']) return false;
        if (!empty($value) && $field['max']!==null && $value>$field['max']) return false;
        return true;
    }

    public function registerEmail($field,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_EMAIL,
            'token' => $this->_token,
            'name' => $field,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateEmail(array $field) {
        $value = $this->getValue($field);
        if ($field['required'] && empty($value)) return false;
        if (!empty($value)) {
            /** dot at beginning and end of local address is not allowed
            return (boolean)preg_match(
                '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
                '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
                $value
            );
            **/
            return (boolean)preg_match(
                '/^(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*)'.
                '@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)$/sDi',
                $value
            );
        } else {
            return true;
        }
    }

    public function registerDate($field,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_DATE,
            'token' => $this->_token,
            'name' => $field,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateDate(array $field) {
        $value = $this->getValue($field);
        if ($field['required'] && empty($value)) return false;
        if (!empty($value)) {
            $format = Zira\Config::get('datepicker_date_format');
            $format = preg_quote($format);
            $format = str_replace('M','[\d]', $format);
            $format = str_replace('D','[\d]', $format);
            $format = str_replace('Y','[\d]', $format);
            return (boolean)preg_match(
                '/^'.$format.'$/',
                $value
            );
        } else {
            return true;
        }
    }

    public function registerPhone($field,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_PHONE,
            'token' => $this->_token,
            'name' => $field,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validatePhone(array $field) {
        $value = $this->getValue($field);
        $value = str_replace(' ','',$value);
        $value = str_replace('-','',$value);
        if ($field['required'] && empty($value)) return false;
        if (!empty($value)) {
            return (boolean)preg_match(
                '/^[+][\d]+$/',
                $value
            );
        } else {
            return true;
        }
    }

    public function registerMatch($field1,$field2,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_MATCH,
            'name' => $field2,
            'fields' => array(
                array(
                    'token' => $this->_token,
                    'name' => $field1
                ),
                array(
                    'token' => $this->_token,
                    'name' => $field2
                )
            ),
            'message' => $message
        );
    }

    protected function validateMatch(array $field) {
        $value1 = $this->getValue($field['fields'][0]);
        $value2 = $this->getValue($field['fields'][1]);
        return $value1 == $value2;
    }

    public function registerRegexp($field,$regexp,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_REGEXP,
            'token' => $this->_token,
            'name' => $field,
            'regexp' => $regexp,
            'message' => $message
        );
    }

    protected function validateRegexp(array $field) {
        $value = $this->getValue($field);
        return (boolean)preg_match(
            $field['regexp'],
            $value
        );
    }

    public function registerText($field,$min_length,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_TEXT,
            'token' => $this->_token,
            'name' => $field,
            'min' => $min_length,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateText(array $field) {
        $value = $this->getValue($field);
        $value = trim($value);
        if ($field['required'] && empty($value)) return false;
        if (!empty($value) && !is_string($value)) return false;
        if (!empty($value) && $field['min']>0 && mb_strlen($value,CHARSET)<$field['min']) return false;
        return true;
    }

    public function registerFile($field,$max_size,array $extensions,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_FILE,
            'token' => $this->_token,
            'name' => $field,
            'max' => $max_size,
            'extensions' => $extensions,
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateFile(array $field) {
        $value = $this->getValue($field,Request::FILES);
        $files = array();
        if (!empty($value) && !empty($value['name']) && !empty($value['tmp_name'])) {
            if (is_array($value['tmp_name'])) {
                foreach($value['tmp_name'] as $i=>$tmp_name) {
                    if (empty($tmp_name)) continue;
                    if (!isset($value['name'][$i])) return false;
                    if (empty($value['name'][$i])) continue;
                    $files[$tmp_name] = $value['name'][$i];
                }
            } else {
                $files[$value['tmp_name']] = $value['name'];
            }
        }

        if (!$field['required'] && empty($files)) return true;
        if ($field['required'] && empty($files)) return false;

        foreach($files as $tmp_name=>$name) {
            if (!file_exists($tmp_name)) return false;
            if ($field['max']>0 && filesize($tmp_name)>$field['max']) return false;

            if (!empty($field['extensions'])) {
                $ext = preg_replace('/^.+\.(.+?)$/iu','$1',$name);
                if (!in_array($ext,$field['extensions'])) return false;
            }
        }

        return true;
    }

    public function registerImage($field,$max_size,$required,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_IMAGE,
            'token' => $this->_token,
            'name' => $field,
            'max' => $max_size,
            'extensions' => array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF'),
            'required' => $required,
            'message' => $message
        );
    }

    protected function validateImage(array $field) {
        if (!$this->validateFile($field)) return false;

        $value = $this->getValue($field,Request::FILES);
        if (!empty($value) && !empty($value['name']) && !empty($value['tmp_name'])) {
            if (is_array($value['tmp_name'])) {
                foreach($value['tmp_name'] as $tmp_name) {
                    if (empty($tmp_name)) continue;
                    if (!@getimagesize($tmp_name)) return false;
                }
            } else if (!@getimagesize($value['tmp_name'])) return false;
        }
        return true;
    }

    public function registerCaptcha($message) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return;
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA) return $this->_registerCaptchaRecaptcha($message);
        else return $this->_registerCaptchaDefault($message);
    }

    protected function _registerCaptchaDefault($message) {
        $this->_fields []= array(
            'type' => self::TYPE_CAPTCHA,
            'name' => CAPTCHA_NAME,
            'token' => $this->_token,
            'message' => $message
        );
    }

    protected function _registerCaptchaRecaptcha($message) {
        $this->_fields []= array(
            'type' => self::TYPE_CAPTCHA,
            'name' => CAPTCHA_NAME,
            'message' => $message
        );
    }

    protected function validateCaptcha(array $field) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return true;
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA) return $this->_validateCaptchaRecaptcha($field);
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA_v3) return $this->_validateCaptchaRecaptcha3($field);
        else return $this->_validateCaptchaDefault($field);
    }

    protected function _validateCaptchaDefault(array $field) {
        return Form::isCaptchaValid($field['token'], $this->_method);
    }

    protected function _validateCaptchaRecaptcha(array $field) {
        return Form::isRecaptchaValid(Zira\Config::get('recaptcha_secret_key', ''), Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT));
    }
    
    protected function _validateCaptchaRecaptcha3(array $field) {
        return Form::isRecaptcha3Valid(Zira\Config::get('recaptcha3_secret_key', ''), Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT), str_replace('-', '_', $this->getFormId()));
    }

    public function registerCaptchaLazy($form_id, $message) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return;
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA) return $this->_registerCaptchaLazyRecaptcha($form_id, $message);
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA_v3) return $this->_registerCaptchaLazyRecaptcha3($form_id, $message);
        else return $this->_registerCaptchaLazyDefault($form_id, $message);
    }

    protected function _registerCaptchaLazyDefault($form_id, $message) {
        $this->_fields []= array(
            'type' => self::TYPE_CAPTCHA_LAZY,
            'name' => CAPTCHA_NAME,
            'token' => $this->_token,
            'form_id' => $form_id,
            'message' => $message
        );
    }

    protected function _registerCaptchaLazyRecaptcha($form_id, $message) {
        $this->_fields []= array(
            'type' => self::TYPE_CAPTCHA_LAZY,
            'name' => CAPTCHA_NAME,
            'form_id' => $form_id,
            'message' => $message
        );
    }
    
    protected function _registerCaptchaLazyRecaptcha3($form_id, $message) {
        $this->_fields []= array(
            'type' => self::TYPE_CAPTCHA_LAZY,
            'name' => CAPTCHA_NAME,
            'form_id' => $form_id,
            'message' => $message
        );
    }

    protected function validateCaptchaLazy(array $field) {
        $captcha_type = Zira\Config::get('captcha_type', Zira\Models\Captcha::TYPE_DEFAULT);
        if ($captcha_type == Zira\Models\Captcha::TYPE_NONE) return true;
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA) return $this->_validateCaptchaLazyRecaptcha($field);
        else if ($captcha_type == Zira\Models\Captcha::TYPE_RECAPTCHA_v3) return $this->_validateCaptchaLazyRecaptcha3($field);
        else return $this->_validateCaptchaLazyDefault($field);
    }

    protected function _validateCaptchaLazyDefault(array $field) {
        if ($this->getValue($field)===null && !Zira\Models\Captcha::isActive($field['form_id'])) {
            Zira\Models\Captcha::register($field['form_id']);
            return true;
        } else {
            Zira\Models\Captcha::register($field['form_id']);
            return Form::isCaptchaValid($field['token'], $this->_method);
        }
    }

    protected function _validateCaptchaLazyRecaptcha(array $field) {
        if (Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT)===null && !Zira\Models\Captcha::isActive($field['form_id'])) {
            Zira\Models\Captcha::register($field['form_id']);
            return true;
        } else {
            Zira\Models\Captcha::register($field['form_id']);
            return Form::isRecaptchaValid(Zira\Config::get('recaptcha_secret_key', ''), Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT));
        }
    }
    
    protected function _validateCaptchaLazyRecaptcha3(array $field) {
        if (Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT)===null && !Zira\Models\Captcha::isActive($field['form_id'])) {
            Zira\Models\Captcha::register($field['form_id']);
            return true;
        } else {
            Zira\Models\Captcha::register($field['form_id']);
            return Form::isRecaptcha3Valid(Zira\Config::get('recaptcha3_secret_key', ''), Zira\Request::post(Zira\Models\Captcha::RECAPTCHA_RESPONSE_INPUT), str_replace('-', '_', $this->getFormId()));
        }
    }

    public function registerExists($field,$class,$property,$message) {
        $this->_fields []= array(
            'type' => self::TYPE_EXISTS,
            'token' => $this->_token,
            'name' => $field,
            'class' => $class,
            'property' => $property,
            'message' => $message
        );
    }

    protected function validateExists(array $field) {
        $value = $this->getValue($field);
        try {
            $collection = new Zira\Db\Collection($field['class']);
            $co = $collection->count()
                        ->where($field['property'],'=',$value)
                        ->get('co');
            return $co==0;
        } catch (\Exception $e) {
            Zira::getInstance()->exception($e);
            return false;
        }
    }

    public function registerCustom(array $class_method,$fields,$message) {
        if (is_array($fields)) $name = reset($fields);
        else $name = (string)$fields;
        $this->_fields []= array(
            'type' => self::TYPE_CUSTOM,
            'class_method' => $class_method,
            'token' => $this->_token,
            'name' => $name,
            'fields' => $fields,
            'message' => $message
        );
    }

    protected function validateCustom($field) {
        $fields = $field['fields'];
        if (!is_array($fields)) {
            $value = $this->getValue($field);
            return call_user_func($field['class_method'],$value);
        } else {
            $args = array();
            foreach($fields as $name) {
                $field['name'] = $name;
                $args[]=$this->getValue($field);
            }
            return call_user_func_array($field['class_method'],$args);
        }
    }

    public function validate() {
        foreach($this->_fields as $field) {
            if (!$this->validateField($field)) {
                $this->_message = $field['message'];
                $this->_error_field = $field['name'];
                return false;
            }
        }
        return true;
    }

    protected function validateField(array $field) {
        if ($field['type']==self::TYPE_STRING && !$this->validateString($field)) return false;
        if ($field['type']==self::TYPE_NO_TAGS && !$this->validateNoTags($field)) return false;
        if ($field['type']==self::TYPE_UTF8 && !$this->validateUtf8($field)) return false;
        if ($field['type']==self::TYPE_NUMBER && !$this->validateNumber($field)) return false;
        if ($field['type']==self::TYPE_EMAIL && !$this->validateEmail($field)) return false;
        if ($field['type']==self::TYPE_DATE && !$this->validateDate($field)) return false;
        if ($field['type']==self::TYPE_MATCH && !$this->validateMatch($field)) return false;
        if ($field['type']==self::TYPE_PHONE && !$this->validatePhone($field)) return false;
        if ($field['type']==self::TYPE_REGEXP && !$this->validateRegexp($field)) return false;
        if ($field['type']==self::TYPE_TEXT && !$this->validateText($field)) return false;
        if ($field['type']==self::TYPE_FILE && !$this->validateFile($field)) return false;
        if ($field['type']==self::TYPE_IMAGE && !$this->validateImage($field)) return false;
        if ($field['type']==self::TYPE_EXISTS && !$this->validateExists($field)) return false;
        if ($field['type']==self::TYPE_CAPTCHA && !$this->validateCaptcha($field)) return false;
        if ($field['type']==self::TYPE_CAPTCHA_LAZY && !$this->validateCaptchaLazy($field)) return false;
        if ($field['type']==self::TYPE_CUSTOM && !$this->validateCustom($field)) return false;
        return true;
    }
}