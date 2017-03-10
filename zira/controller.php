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
        //View::addThemeAssets();
    }

    public function _after() {

    }
}