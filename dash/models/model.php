<?php
/**
 * Zira project.
 * model.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Dash\Windows\Window;

abstract class Model {
    protected $_window;

    public function __construct(Window $window) {
        $this->_window = $window;
    }

    public function getWindow() {
        return $this->_window;
    }

    public function getJSClassName() {
        return $this->_window->getJSClassName();
    }
}