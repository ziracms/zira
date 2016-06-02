<?php
/**
 * Zira project.
 * rated.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Rated extends Zira\Widget {
    protected $_title = 'Top records';

    protected function _init() {
        $this->setCaching(true);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }

    protected function getKey() {
        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        return self::CACHE_PREFIX.'.'.strtolower(str_replace('\\','.',get_class($this))).'.side'.intval($is_sidebar).'.grid'.intval($is_grid).'.'.Zira\Locale::getLanguage();
    }

    public static function getTopRatedRecordsList($limit = null, $last_id = null) {
        if ($limit === null) $limit = Zira\Config::get('records_limit', 10);

        $category_ids = array(Zira\Category::ROOT_CATEGORY_ID);
        $categories = Zira\Models\Category::getCollection()->get();
        foreach($categories as $category) {
            $category_ids []= $category->id;
        }

        $query = Zira\Models\Record::getCollection();
        foreach($category_ids as $index=>$category_id) {
            if ($index>0) {
                $query->union();
            }
            $query->open_query();
            $query->select('id', 'rating');
            $query->where('category_id', '=', $category_id);
            $query->and_where('language', '=', Zira\Locale::getLanguage());
            $query->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED);
            if ($last_id!==null) {
                $query->and_where('id', '<', $last_id);
            }
            $query->order_by('rating', 'desc');
            $query->limit($limit);
            $query->close_query();
        }
        $query->merge();
        $query->order_by('rating', 'desc');
        $query->limit($limit);

        $rows = $query->get();

        if (!$rows) return array();

        $query = Zira\Models\Record::getCollection()
                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
                        ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ;

        $record_ids = array();
        foreach($rows as $index=>$row) {
            $record_ids []= $row->id;
        }
        $query->where('id','in',$record_ids);

        $_rows = $query->get();
        foreach($_rows as $_row) {
            $records[] = $_row;
        }

        usort($records, array(Zira\Models\Record::getClass(), 'sortByRatingDesc'));

        return $records;
    }

    protected function _render() {
        $limit = Zira\Config::get('records_limit', 10);

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        $data = array(
            'title' => Zira\Locale::t('Top rated'),
            'url' => '',
            'records' => self::getTopRatedRecordsList($limit),
            'grid' => $is_grid,
            'settings' => array(
                'comments_enabled' => true,
                'rating_enabled' => true,
                'display_date' => true,
                'sidebar' => $is_sidebar
            )
        );

        Zira\View::renderView($data, 'zira/widgets/rated');
    }
}