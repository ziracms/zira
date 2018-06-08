<?php
/**
 * Zira project.
 * records.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Records extends Zira\Controller {
    /**
     * AJAX action
     * Loads category records
     */
    public function index() {
        if (Zira\Request::isPost()) {
            $category_id = (int)Zira\Request::post('category_id');
            $last_id = (int)Zira\Request::post('last_id');
            $page = (int)Zira\Request::post('page');

            if (!$category_id || ($last_id<=0 && $page<=0)) return;

            $category = new Zira\Models\Category($category_id);
            if (!$category->loaded()) return;

            Zira\Category::setCurrent($category);
            Zira\Category::setChilds(null);
            Zira\Content\Category::content($page, $last_id, true);
        }
    }
    
    /**
     * AJAX action
     * Loads records calendar
     */
    public function calendar() {
        Zira\View::setAjax(true);
        if (Zira\Request::isPost()) {
            $month = (int)Zira\Request::post('month');
            $year = (int)Zira\Request::post('year');

            if (!$year) return;
            $date = $year.'-'.(str_pad($month+1, 2, '0', STR_PAD_LEFT)).'-';

            $rows = Zira\Models\Search::getCollection()
                            ->select('keyword')
                            ->where('language', '=', Zira\Locale::getLanguage())
                            ->and_where('keyword', 'like', $date.'%')
                            ->group_by('keyword')
                            ->get();
                          
            $days = array();
            foreach($rows as $row) {
                $parts = explode('-', $row->keyword);
                if (count($parts)!=3) continue;
                $days []= intval($parts[2]);
            }
            
            Zira\Page::render(array('days'=>$days));
        }
    }
}