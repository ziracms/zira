<?php
/**
 * Zira project.
 * blockhtml.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;

class Blockhtml extends Blocktext {
    public $item;
    protected $_help_url = 'zira/help/tinymce';

    public function init() {
        parent::init();
        $this->setWysiwyg(true);
    }
}