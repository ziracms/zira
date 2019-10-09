<?php
/**
 * Zira project.
 * button.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Widgets;

use Dash\Dash;
use Zira;

class Button extends Zira\Widget {

    protected function _init() {
        $this->setEditable(false);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_BODY_TOP);
    }

    protected function _render() {
        //if (!Zira\View::isViewExists('dash/button')) return;
        Zira\Helper::setAddingLanguageToUrl(false);
        Zira\View::renderView(array(), 'dash/button');
        Zira\Helper::setAddingLanguageToUrl(true);
    }
}