<?php
/**
 * Zira project.
 * share.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Share extends Zira\Widget {
    protected $_title = 'Social buttons';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT_BOTTOM);
    }

    protected function _render() {
        Zira\View::renderView(array(
            'url' => Zira\Helper::url(Zira\Router::getRequest(), true, true),
            'title' => strip_tags(Zira\View::getLayoutData(Zira\View::VAR_TITLE))
        ), 'zira/widgets/share');
    }
}