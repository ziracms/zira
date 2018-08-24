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

//        Zira\Page::setLayout(Zira\View::LAYOUT_ALL_SIDEBARS);
//        Zira\View::setRenderDbWidgets(false);
            
        Zira\Page::setContentView(array('categories'=>$categories), 'zira/map');
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
        header('Content-Disposition: attachment; filename="'.addcslashes(Zira\Helper::basename($real_path),'"').'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));
        readfile($real_path);
        exit;
    }
    
    /**
     * Thumbs generator
     */
    function thumbnailer($uri) {
        if (empty($uri)) exit;
        $path = trim(rawurldecode($uri), '/');
        if (strpos($path, UPLOADS_DIR.'/'.THUMBS_DIR.'/'.CUSTOM_THUMBS_ACTION.'/')!==0) exit;
        $path = substr($path, strlen(UPLOADS_DIR.'/'.THUMBS_DIR.'/'.CUSTOM_THUMBS_ACTION.'/'));
        if (empty($path)) exit;
        $parts = explode('/', $path);
        if (count($parts)<2) exit;
        $size=array_shift($parts);
        $sizes = explode('x', $size);
        if (count($sizes)!==2) exit;
        $width = intval($sizes[0]);
        $height = intval($sizes[1]);
        if ($width < Zira\Image::CUSTOM_THUMB_MIN_WIDTH || $height < Zira\Image::CUSTOM_THUMB_MIN_HEIGHT) exit;
        $size = $width.'x'.$height;
        $path = UPLOADS_DIR.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $parts);
        $src_path = ROOT_DIR . DIRECTORY_SEPARATOR . $path;
        $ext = 'thumb';
        $p = strrpos($src_path, '.');
        if ($p!==false) $ext = strtolower(substr($src_path, $p+1));
        if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png') exit;
        $_path = substr($path, 0, (int)strrpos($path, DIRECTORY_SEPARATOR));
        $_path = substr($_path, strlen(UPLOADS_DIR . DIRECTORY_SEPARATOR));
        $savedir = THUMBS_DIR . DIRECTORY_SEPARATOR . CUSTOM_THUMBS_ACTION . DIRECTORY_SEPARATOR . $size;
        if (!empty($_path)) $savedir .= DIRECTORY_SEPARATOR . $_path;
        $save_path = Zira\File::getAbsolutePath($savedir);
        $name = ltrim(substr($path, (int)strrpos($path, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
        if (file_exists($save_path . DIRECTORY_SEPARATOR . $name) && 
            filesize($save_path . DIRECTORY_SEPARATOR . $name)>0
        ) {
            // why we are here ?
            header('Content-Type: image/'.$ext);
            echo file_get_contents($save_path . DIRECTORY_SEPARATOR . $name);
            exit;
        }
        if (file_exists($src_path) && 
            Zira\Image::createThumb($src_path, $save_path . DIRECTORY_SEPARATOR . $name, $width, $height) && 
            file_exists($save_path . DIRECTORY_SEPARATOR . $name) && 
            filesize($save_path . DIRECTORY_SEPARATOR . $name)>0
        ) {
            header('Content-Type: image/'.$ext);
            echo file_get_contents($save_path . DIRECTORY_SEPARATOR . $name);
            exit;
        }
        exit;
    }
}
