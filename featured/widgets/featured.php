<?php
/**
 * Zira project.
 * featured.php
 * (c)2016 http://dro1d.ru
 */

namespace Featured\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Featured extends Widget {
    protected $_title = 'Featured records';

    protected function _init() {
        $this->setEditable(true);
        $this->setCaching(true);
        $this->setPlaceholder(View::VAR_CONTENT_BOTTOM);
    }

    protected function getKey() {
        $suffix = '';
        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        $suffix .= '.side'.intval($is_sidebar).'.grid'.intval($is_grid);

        return parent::getKey().$suffix;
    }

    protected function _render() {
        $rows = \Featured\Models\Featured::getRecords();
        if (!$rows) return;

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        //$is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;
        $is_grid = Zira\Config::get('site_records_grid', 1) && !$is_sidebar;

        Zira\Page::runRecordsHook($rows);
        
        Zira\View::renderView(array(
            'title' => Zira\Locale::tm('Featured', 'featured'),
            'records' => $rows,
            'grid' => $is_grid
        ),'featured/widget');
    }
}