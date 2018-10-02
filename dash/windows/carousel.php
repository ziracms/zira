<?php
/**
 * Zira project.
 * carousel.php
 * (c)2018 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Carousel extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-film';
    protected static $_title = 'Carousel';

    //protected $_help_url = 'zira/help/carousel';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setViewSwitcherEnabled(true);
        
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Title'), 'glyphicon glyphicon-font', 'desk_call(dash_files_carousel_title, this);', 'create', false)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('URL address'), 'glyphicon glyphicon-link', 'desk_call(dash_files_carousel_link, this);', 'edit', true)
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_files_carousel_widget, this);', 'create', true, array('typo'=>'widget'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Title'), 'glyphicon glyphicon-font', 'desk_call(dash_files_carousel_title, this);', 'create', false)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Description'), 'glyphicon glyphicon-list-alt', 'desk_window_edit_item(this);', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('URL address'), 'glyphicon glyphicon-link', 'desk_call(dash_files_carousel_link, this);', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_files_carousel_widget, this);', 'create', true, array('typo'=>'widget'))
        );
    }

    public function create() {
        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_files_carousel_desc, this);'
            )
        );
        
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_files_carousel_load, this);'
            )
        );

        $this->addStrings(array(
            'Enter title',
            'Enter description',
            'Enter URL address'
        ));
    }

    public function load() {
        if (empty($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $file = trim($this->item,DIRECTORY_SEPARATOR);
        if ($file==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
        if (strpos($file, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR.Zira\File::WIDGETS_FOLDER.DIRECTORY_SEPARATOR)!==0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $file) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $file)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $content = file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . $file);
        if (empty($content)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $data = @unserialize($content);
        if (!is_object($data) || !$data->path) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $data->path = trim($data->path, DIRECTORY_SEPARATOR);
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        
        $thumbs_width = Zira\Config::get('thumbs_width');
        $thumbs_height = Zira\Config::get('thumbs_height');
        $thumbs_width = Zira\Config::get('carousel_thumbs_width', $thumbs_width);
        $thumbs_height = Zira\Config::get('carousel_thumbs_height', $thumbs_height);

        $name = Zira\Helper::basename($file);
        $p=strrpos($name,'.');
        if ($p!==false) {
            $name = substr($name,0,$p);
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . Zira\Helper::html($data->title ? $data->title : $name));

        $d = @opendir(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path);
        if (!$d) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $images = array();
        while(($f = readdir($d))!==false) {
            if ($f=='.' || $f=='..') continue;
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . $data->path . DIRECTORY_SEPARATOR . $f;
            if (is_dir($path)) continue;
            $ext = '';
            $p=strrpos($f,'.');
            if ($p!==false) {
                $ext = strtolower(substr($f,$p+1));
            }
            if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) continue;
            $images []= str_replace(DIRECTORY_SEPARATOR, '/', $data->path . DIRECTORY_SEPARATOR . $f);
        }
        closedir($d);
        
        $widgets = array();
        $rows = Zira\Models\Widget::getCollection()
                                ->where('name','=',Zira\File::WIDGET_CLASS)
                                ->get()
                                ;
        foreach($rows as $row) {
            $widgets[] = $row->params;
        }
        
        $items = array();
        foreach($images as $image) {
            $name = Zira\Helper::basename($image);
            $link = $data->links && array_key_exists(Zira\Helper::urlencode($image), $data->links) ? $data->links[Zira\Helper::urlencode($image)] : '';
            $description = $data->descriptions && array_key_exists(Zira\Helper::urlencode($image), $data->descriptions) ? $data->descriptions[Zira\Helper::urlencode($image)] : '';
            $items []= $this->createBodyItem(Zira\Helper::html($name), Zira\Helper::html($description), Zira\Helper::baseUrl(Zira\Image::getCustomThumbUrl($image, $thumbs_width, $thumbs_height)), $image, null, false, array('description'=>$description,'link'=>$link));
        }

        $this->setBodyItems($items);
        
        $this->setFooterContent($data->path);

        $this->setData(array(
            'items' => array($this->item),
            'widget_title' => $data->title,
            'widget_exists' => in_array(Zira\Helper::basename($file),$widgets)
        ));
    }
}