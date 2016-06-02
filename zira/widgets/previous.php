<?php
/**
 * Zira project.
 * previous.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Previous extends Zira\Widget {
    protected $_title = 'Category previous records';

    protected function _init() {
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT_BOTTOM);
    }

    protected function _render() {
        $record_id = Zira\Page::getRecordId();
        if (!$record_id) return;

        $category = Zira\Category::current();
        if (!$category) return;

        $limit = Zira\Config::get('records_limit', 10);

        $comments_enabled = $category->comments_enabled !== null ? $category->comments_enabled : Zira\Config::get('comments_enabled', 1);
        $rating_enabled = $category->rating_enabled !== null ? $category->rating_enabled : Zira\Config::get('rating_enabled', 0);
        $display_author = $category->display_author !== null ? $category->display_author : Zira\Config::get('display_author', 0);
        $display_date = $category->display_date !== null ? $category->display_date : Zira\Config::get('display_date', 0);

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        $records = Zira\Page::getRecords($category, false, $limit, $record_id, false);
        if (!count($records)) return;

        $data = array(
            'title' => Zira\Locale::t('View also'),
            'url' => '',
            'records' => $records,
            'grid' => $is_grid,
            'settings' => array(
                'comments_enabled' => $comments_enabled,
                'rating_enabled' => $rating_enabled,
                'display_author' => $display_author && !$is_grid,
                'display_date' => $display_date,
                'sidebar' => $is_sidebar
            )
        );

        Zira\View::renderView($data, 'zira/widgets/category');
    }
}