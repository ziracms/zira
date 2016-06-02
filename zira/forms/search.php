<?php
/**
 * Zira project.
 * search.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\Request;
use Zira\View;

class Search extends Form {
    protected $_id = 'search-form';
    protected $_extended = false;

    public function __construct() {
        $this->_is_token_unique = true;
        parent::__construct($this->_id);
    }

    public function setExtended($extended) {
        $this->_extended = $extended;
    }

    protected function _init() {
        $this->setUrl('search');
        $this->setMethod(Request::GET);
        $this->setRenderPanel(false);
        if (!$this->_extended) {
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ zira_init_search(jQuery(\'#'.$this->getId().'\')); });';
        $script .= Helper::tag_close('script');
        View::addHTML($script, View::VAR_BODY_BOTTOM);
        }
    }

    public function getValue($name, $default = NULL) {
        return Request::get($name, $default);
    }

    public function setValue($name, $value) {
        Request::setGet($name, $value);
    }

    public function input($label, $name, array $attributes = NULL) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'text';
        $attributes['id'] = $name;
        $attributes['name'] = $name;

        $_value = trim($this->getValue($name));

        if (isset($_value)) $attributes['value'] = $_value;
        else if (!isset($attributes['value'])) $attributes['value'] = '';

        return Helper::tag_short('input',$attributes);
    }

    protected function _render() {
        if (!$this->_extended) {
            return $this->_renderSimple();
        } else {
            return $this->_renderExtended();
        }
    }

    protected function _renderSimple() {
        $html = $this->open(array('class'=>'search-simple-form navbar-form navbar-right','role'=>'search'));
        $html .= Helper::tag_open('div',array('class'=>'form-group input-group'));
        $html .= $this->input(null,'text', array('class'=>$this->_input_class, 'placeholder'=>Locale::t('Search'),'autocomplete'=>'off'));
        $html .= Helper::tag_open('span',array('class'=>'input-group-btn'));
        $html .= Helper::tag_open('button',array('type'=>'submit','class'=>'btn btn-default'));
        $html .= Helper::tag('span', null, array('class'=>'glyphicon glyphicon-search'));
        $html .= Helper::tag_close('button');
        $html .= Helper::tag_close('span');
        $html .= Helper::tag_close('div');
        $html .= $this->close();
        return $html;
    }

    protected function _renderExtended() {
        $html = $this->open(array('class'=>'search-extended-form','role'=>'search'));
        $html .= Helper::tag_open('div',array('class'=>'form-group input-group'));
        $html .= Helper::tag_open('span',array('class'=>'input-group-addon'));
        $html .= Helper::tag('span', null, array('class'=>'glyphicon glyphicon-search'));
        $html .= Helper::tag_close('span');
        $html .= $this->input(null,'text', array('class'=>$this->_input_class));
        $html .= Helper::tag_open('span',array('class'=>'input-group-btn'));
        $html .= Helper::tag_open('button',array('type'=>'submit','class'=>'btn btn-default'));
        $html .= Locale::t('Search');
        $html .= Helper::tag_close('button');
        $html .= Helper::tag_close('span');
        $html .= Helper::tag_close('div');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->setMethod(Request::GET);
        $validator->registerCustom(array(get_class(), 'validateString'), 'text', Locale::t('Invalid search text'));
        $validator->registerCustom(array(get_class(), 'validateNoTags'), 'text', Locale::t('Search text contains bad character'));
    }

    public static function validateString($ignore) {
        return self::_validateString('text', \Zira\Models\Search::MIN_CHARS, 255, false);
    }

    public static function _validateString($name, $min, $max, $required) {
        $value = Request::get($name);
        $value = trim($value);
        if ($required && empty($value)) return false;
        if (!empty($value) && !is_string($value)) return false;
        if (!empty($value) && $min>0 && mb_strlen($value,CHARSET)<$min) return false;
        if (!empty($value) && $max>0 && mb_strlen($value,CHARSET)>$max) return false;
        return true;
    }

    public static function validateNoTags($ignore) {
        return self::_validateNoTags('text');
    }

    public static function _validateNoTags($name) {
        $value = Request::get($name);
        if (empty($value)) return true;
        if (strpos($value, '<')!==false || strpos($value, '>')!==false) return false;
        if (strpos($value, '%')!==false) return false;
        return true;
    }
}