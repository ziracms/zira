<?php
/**
 * Zira project.
 * search.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Forms;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\Request;
use Zira\View;

class Search extends Form {
    protected $_id = 'forum-search-form';
    protected $_extended = false;
    protected $_align_right = false;

    public function __construct() {
        $this->_is_token_unique = true;
        parent::__construct($this->_id);
    }

    public function setExtended($extended) {
        $this->_extended = $extended;
    }

    public function setAlignRight($align_right) {
        $this->_align_right = $align_right;
    }

    protected function _init() {
        $this->setUrl(\Forum\Forum::ROUTE.'/search');
        $this->setMethod(Request::GET);
        $this->setRenderPanel(false);
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

    public function hidden($name, array $attributes = NULL) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'hidden';
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
        $align_class = $this->_align_right ? 'navbar-right' : 'navbar-left';
        $html = $this->open(array('class'=>'search-simple-form navbar-form '.$align_class,'role'=>'search'));
        $html .= Helper::tag_open('div',array('class'=>'form-group input-group'));
        $html .= $this->input(null,'text', array('class'=>$this->_input_class, 'placeholder'=>Locale::t('Search'),'autocomplete'=>'off'));
        if ($this->getValue('forum_id')) {
            $html .= $this->hidden('forum_id');
        }
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
        if ($this->getValue('forum_id')) {
            $html .= $this->hidden('forum_id');
        }
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
        $validator->registerCustom(array(get_class(), 'validateNumber'), 'forum_id', Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'validateString'), 'text', Locale::tm('Invalid search text','forum'));
        $validator->registerCustom(array(get_class(), 'validateNoTags'), 'text', Locale::tm('Search text contains bad character','forum'));
    }

    public static function validateNumber($ignore) {
        return self::_validateNumber('forum_id', 0, 255, false);
    }

    public static function _validateNumber($name, $min, $max, $requried) {
        $value = Request::get($name);
        if ($requried && $value===null) return false;
        if ($value!==null && !is_numeric($value)) return false;
        if ($value!==null && $min!==null && $value<$min) return false;
        if ($value!==null && $max!==null && $value>$max) return false;
        return true;
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