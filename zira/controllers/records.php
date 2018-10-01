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
    
    /**
     * AJAX action
     * Loads gallery images
     */
    public function images() {
        if (Zira\Request::isPost()) {
            $record_id = (int)Zira\Request::post('record_id');
            $page = (int)Zira\Request::post('page');

            if (!$record_id) return;

            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) return;
            
            $category = null;
            if ($record->category_id) {
                $category = new Zira\Models\Category($record->category_id);
                if (!$category->loaded()) return;
            }
            
            if (!$category) {
                $gallery_enabled = Zira\Config::get('gallery_enabled', 1);
            } else {
                $gallery_enabled = $category->gallery_enabled!==null ? $category->gallery_enabled : Zira\Config::get('gallery_enabled', 1);
            }
            
            if ((($category && $category->access_check) || $record->access_check) && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORD)) return;
            
            if ($category) {
                $category_gallery_check = $category->gallery_check;
            } else {
                $category_gallery_check = false;
            }
            
            if (($record->gallery_check || $category_gallery_check || Zira\Config::get('gallery_check')) &&
               !Zira\Permission::check(Zira\Permission::TO_VIEW_GALLERY)
            ) {
                $access_gallery = false;
            } else {
                $access_gallery = true;
            }
        
            if (!$gallery_enabled || !$access_gallery) return;
            
            $images_limit = intval(Zira\Config::get('gallery_limit', 0));
            $images_co = Zira\Page::getRecordImagesCount($record->id);
            
            if (!$images_co) return;
            
            $pages = ceil($images_co / $images_limit);
            if ($page > $pages) return;
            if ($page < 1) $page = 1;
            if ($images_co>0) {
                $images = Zira\Page::getRecordImages($record->id, $images_limit, ($page-1)*$images_limit);
            } else {
                $images = array();
            }
            
            if (empty($images)) return;
            Zira\Page::setGallery($images, $access_gallery, $images_limit, $images_co, $record->id);
            Zira\Page::setPlaceholdersData();
            require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'tpl.php');
            renderGallery();
        }
    }
}