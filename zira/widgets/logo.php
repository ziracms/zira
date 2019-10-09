<?php
/**
 * Zira project.
 * logo.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Logo extends Zira\Widget {
    protected $_title = 'Logo';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(1);
        $this->setPlaceholder(Zira\View::VAR_HEADER);
    }

    protected function _render() {
        $logo_width = (int)Zira\Config::get('site_logo_width');
        $logo_height = (int)Zira\Config::get('site_logo_height');
        if ($logo_width && $logo_height) {
            $logo_size = array($logo_width, $logo_height);
        } else {
            $logo_size = null;
        }
        Zira\View::renderView(array(
            'logo' => Zira\Config::get('site_logo'),
            'logo_size' => $logo_size,
            'title' => Zira\Locale::t(Zira\Config::get('site_name')),
            'slogan' => Zira\Locale::t(Zira\Config::get('site_slogan'))
        ), 'zira/widgets/logo');
    }
}