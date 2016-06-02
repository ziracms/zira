<?php
/**
 * Zira project.
 * languages.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Languages extends Zira\Widget {
    protected $_title = 'Languages panel';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_HEADER);
    }

    protected function _render() {
        if (count(Zira\Config::get('languages'))<2) return;
        Zira\View::renderView(array(), 'zira/widgets/languages');
    }
}