<?php
/**
 * Zira project
 * widget.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

abstract class Widget {
    const CACHE_PREFIX = 'widget';

    protected $_placeholder = View::VAR_CONTENT;
    protected $_order = 0;
    protected $_caching = false;
    protected $_editable = true;
    protected $_dynamic = false;
    protected $_data = null;
    protected $_title = null;

    public function __construct() {
        $this->_init();
    }

    abstract protected function _init();
    abstract protected function _render();

    public static function getClass() {
        return get_called_class();
    }

    public function getPlaceholder() {
        return $this->_placeholder;
    }

    public function setPlaceholder($placeholder) {
        $this->_placeholder = $placeholder;
    }

    public function getOrder() {
        return $this->_order;
    }

    public function setOrder($order) {
        $this->_order = $order;
    }

    public function getCaching() {
        return $this->_caching;
    }

    public function setCaching($caching) {
        $this->_caching = $caching;
    }

    public function setEditable($editable) {
        $this->_editable = $editable;
    }

    public function isEditable() {
        return $this->_editable;
    }

    public function setDynamic($dynamic) {
        $this->_dynamic = $dynamic;
    }

    public function isDynamic() {
        return $this->_dynamic;
    }

    public function setData($data) {
        $this->_data = $data;
    }

    public function getData() {
        return $this->_data;
    }

    public function getTitle() {
        return $this->_title!==null ? $this->_title : $this->getClass();
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    protected function getKey() {
        return self::CACHE_PREFIX.'.'.strtolower(str_replace('\\','.',get_class($this))).'.'.Locale::getLanguage();
    }

    public function render() {
        if ($this->_caching && ($cache=Cache::get($this->getKey()))) {
            echo $cache;
            return;
        }
        $ob_started = false;
        if ($this->_caching && Config::get('caching')) {
            ob_start();
            $ob_started = true;
        }
        $this->_render();
        if ($ob_started) {
            $output = ob_get_clean();
            Cache::set($this->getKey(), $output);
            echo $output;
        }
    }
}