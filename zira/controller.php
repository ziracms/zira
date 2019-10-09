<?php
/**
 * Zira project
 * abstract.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

abstract class Controller {
    public function _before() {
        View::addDefaultAssets();
    }

    public function _after() {

    }
}