<?php
/**
 * Zira project
 * model.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

abstract class Form extends Form\Factory {
    protected $_request_values = array();

    public function __construct($id, $url=null, $method = Request::POST) {
        parent::__construct($id, $url, $method);
        $this->_init();

        if ($this->_ajax) {
            View::addUploadJS();
        }
    }

    abstract protected function _init();
    abstract protected function _render();
    abstract protected function _validate();

    protected function _update_values() {
        if ($this->_method == Request::GET) {
            $this->_request_values = Request::get();
        } else if ($this->_method == Request::POST) {
            if (!$this->_multipart) {
                $this->_request_values = Request::post();
            } else {
                $this->_request_values = array_merge(Request::post(), Request::file());
            }
        }
    }

    public function getValue($name, $default = null) {
        $_name = $this->getFieldName($name);
        $request_value = isset($this->_request_values[$_name]) ? $this->_request_values[$_name] : null;
        if ($request_value !== null) return $request_value;
        if (isset($this->_values[$name])) return $this->_values[$name];
        return $default;
    }

    public function setValue($name, $value) {
        $_name = Form\Form::getFieldName($this->_token, $name);
        if (isset($this->_request_values[$_name])) $this->_request_values[$_name] = $value;
        $this->_values[$name] = $value;
    }

    public function updateValues(array $values) {
        foreach($values as $key=>$value) {
            $this->setValue($key, $value);
            $_name = Form\Form::getFieldName($this->_token, $key);
            if ($this->_method == Request::POST) {
                Request::setPost($_name, $value);
            } else if ($this->_method == Request::GET) {
                Request::setGet($_name, $value);
            }
        }
    }

    public function isValid() {
        $this->_update_values();
        $this->_validate();
        $result = $this->validate();
        return $result;
    }

    public function __toString() {
        return $this->_render();
    }
}