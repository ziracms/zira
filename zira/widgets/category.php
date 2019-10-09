<?php
/**
 * Zira project.
 * category.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Category extends Zira\Widget {
    protected $_title = 'Category records';
    protected static $_titles;

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(true);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_LEFT);
    }

    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = Zira\Models\Category::getCollection()->get();
            foreach($rows as $row) {
                self::$_titles[$row->id] = Zira\Locale::t($row->title) . ' ('.$row->name.')';
            }
        }
        return self::$_titles;
    }

    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            $titles = $this->getTitles();
            if (empty($titles) || !array_key_exists($this->getData(), $titles)) return parent::getTitle();
            return Zira\Locale::t('Records') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }

    protected function getKey() {
        $id = $this->getData();
        $suffix = '';
        if (!empty($id) && is_numeric($id)) $suffix = '.'.$id;

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;

        $suffix .= '.side'.intval($is_sidebar);

        return parent::getKey().$suffix;
    }

    protected function _render() {
        $id = $this->getData();
        if (!is_numeric($id)) return;

        $limit = Zira\Config::get('widget_records_limit', 5);

        $category = new Zira\Models\Category(intval($id));
        if (!$category->loaded()) return;

        $comments_enabled = $category->comments_enabled !== null ? $category->comments_enabled : Zira\Config::get('comments_enabled', 1);
        $rating_enabled = $category->rating_enabled !== null ? $category->rating_enabled : Zira\Config::get('rating_enabled', 0);
        //$display_author = $category->display_author !== null ? $category->display_author : Zira\Config::get('display_author', 0);
        $display_author = false; // disabled
        $display_date = $category->display_date !== null ? $category->display_date : Zira\Config::get('display_date', 0);

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $grid = Zira\Config::get('site_records_grid', 1);

        $records = Zira\Page::getWidgetRecords($category, false, $limit, null, Zira\Config::get('category_childs_list', true));
        if (empty($records)) return;
        
        $data = array(
            'title' => Zira\Locale::t($category->title),
            'url' => Zira\Page::generateCategoryUrl($category->name),
            'records' => $records,
            'grid' => !$is_sidebar ? $grid : 0,
            'settings' => array(
                'comments_enabled' => $comments_enabled,
                'rating_enabled' => $rating_enabled,
                'display_author' => $display_author && !$is_sidebar && !$grid,
                'display_date' => $display_date,
                'sidebar' => $is_sidebar
            )
        );

        Zira\View::renderView($data, 'zira/widgets/category');
    }
}