<?php
/**
 * Zira project.
 * recordhtml.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;

class Recordhtml extends Recordtext {
    public $item;

    public function init() {
        parent::init();
        $this->setWysiwyg(true);
    }

    public function create() {
        parent::create();

        $this->includeJS('dash/recordhtml');
    }

    public function getHtmlOnLoadJs() {
        return parent::getHtmlOnLoadJs().
                'desk_call(dash_recordhtml_load, this);';
    }
}