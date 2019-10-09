<?php
/**
 * Zira project.
 * category.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Featured\Widgets;

use Zira;
use Zira\View;
use Zira\Widget;

class Category extends Widget {
    protected $_title = 'Category featured records';

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

        $category_id = 0;
        if (Zira\Category::current()) $category_id = Zira\Category::current()->id;
        
        $suffix .= '.cat'.intval($category_id).'.side'.intval($is_sidebar);

        return parent::getKey().$suffix;
    }

    protected function _render() {
        if (!Zira\Category::current()) return;
        
        $rows = \Featured\Models\Featured::getCategoryRecords(Zira\Category::current()->id);
        if (!$rows) return;

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $grid = Zira\Config::get('site_records_grid', 1);

        Zira\Page::runRecordsHook($rows, true);
        
        Zira\View::renderView(array(
            'title' => Zira\Locale::tm('Featured', 'featured'),
            'records' => $rows,
            'grid' => !$is_sidebar ? $grid : 0
        ),'featured/widget');
    }
}