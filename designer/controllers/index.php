<?php
/**
 * Zira project.
 * index.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Controllers;

use Zira;
use Designer;

class Index extends Zira\Controller {
    public function index() {
        header('Content-Type: text/css');
        echo Designer\Designer::getStyle(true);
    }
}