<?php
/**
 * Zira project.
 * blockhtml.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;

class Blockhtml extends Blocktext {
    public $item;

    public function init() {
        parent::init();
        $this->setWysiwyg(true);
    }
}