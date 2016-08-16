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
        $rows = Zira\Models\Record::getCollection()
                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
                        ->join(\Featured\Models\Featured::getClass(), array('featured_id' => 'id', 'featured_sort_order' => 'sort_order'))
                        //->join(Zira\Models\User::getClass(), array('author_username'=>'username', 'author_firstname'=>'firstname', 'author_secondname'=>'secondname'))
                        ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ->where('language', '=', Zira\Locale::getLanguage())
                        ->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED)
                        ->order_by('featured_sort_order', 'asc')
                        ->get();

        if (!$rows) return;

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        Zira\View::renderView(array(
            'title' => Zira\Locale::tm('Featured', 'featured'),
            'records' => $rows,
            'grid' => $is_grid
        ),'featured/widget');
    }
}