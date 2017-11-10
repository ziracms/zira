<?php
/**
 * Zira project
 * abstract.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

abstract class Controller {
    public function _before() {
        View::addDefaultAssets();
    }

    public function _after() {

    }
}