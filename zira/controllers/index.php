<?php
/**
 * Zira project
 * index.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Index extends Zira\Controller {
    public function _before() {
        parent::_before();
    }

    /**
     * Home page
     */
    public function index() {
        $layout = Zira\Config::get('home_layout');
        if ($layout!==null) {
            Zira\Page::setLayout($layout);
        }
        Zira\Content\Index::content();
    }

    /**
     * Renders record page or category
     *
     * @param $param
     */
    public function page($param) {
        if (!empty($param)) {
            if (Zira\Category::current() && Zira\Category::current()->layout) {
                Zira\Page::setLayout(Zira\Category::current()->layout);
            }
            Zira\Content\Page::content($param, Zira\Page::allowPreview());
        } else if (Zira\Category::current()) {
            if (Zira\Category::current()->layout) {
                Zira\Page::setLayout(Zira\Category::current()->layout);
            }
            Zira\Content\Category::content();
        } else {
            Zira\Response::notFound();
        }
    }

    /**
     * 403 page
     */
    public function forbidden() {
        Zira\Response::forbidden();
    }

    /**
     * 404 page
     */
    public function notfound() {
        Zira\Response::notFound();
    }

    /**
     * Displays CAPTCHA image
     */
    public function captcha() {
        header('Content-Type: image/jpeg');
        Zira\Form\Form::generateCaptcha();
    }

    /**
     * Site map page
     */
    public function map() {
        $categories = Zira\Category::getCategoriesMap();

        Zira\Page::addTitle(Zira\Locale::t('Site map'));
        Zira\Page::addBreadcrumb('sitemap', Zira\Locale::t('Site map'));

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array('categories'=>$categories), 'zira/map');
        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t('Site map'),
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }
    
    /** 
     * File download action
     */
    public function file($id) {
        $id = intval($id);
        if (!$id) Zira\Response::notFound();
        
        $file = new Zira\Models\File($id);
        if (!$file->loaded()) Zira\Response::notFound();
 
        if (strpos($file->path, '..')!==false || strpos($file->path, UPLOADS_DIR.'/')!==0) Zira\Response::forbidden();
        
        $real_path = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file->path);
        if (!file_exists($real_path)) Zira\Response::notFound();
        
        if ($file->record_id) {
            $record = new Zira\Models\Record($file->record_id);
            if (!$record->loaded()) Zira\Response::notFound();
            if ($record->category_id) {
                $category = new Zira\Models\Category($record->category_id);
                if (!$category->loaded()) Zira\Response::notFound();
            }
        }
                
        if (isset($record) && !Zira\Permission::check(Zira\Permission::TO_DOWNLOAD_FILES)) {
            $files_check = Zira\Config::get('files_check') || $record->files_check || (isset($category) && $category->files_check);
            if ($files_check) Zira\Response::forbidden();
        }
        
        $file->download_count++;
        $file->save();
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($real_path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));
        readfile($real_path);
        exit;
    }
}
