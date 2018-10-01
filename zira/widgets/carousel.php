<?php
/**
 * Zira project.
 * carousel.php
 * (c)2018 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Carousel extends Zira\Widget {
    protected $_title = 'Carousel';

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT_BOTTOM);
    }

    public function getTitle() {
        $id = $this->getData();
        $id = Zira\Helper::basename($id);
        $p=strrpos($id,'.');
        if ($p!==false) {
            $id = substr($id,0,$p);
        }
        return Zira\Locale::t($this->_title).' - '.$id;
    }

    protected function _render() {
        $id = $this->getData();
        if (empty($id) || strpos($id, '..')!==false) return;

        $file = trim(UPLOADS_DIR . DIRECTORY_SEPARATOR . Zira\File::WIDGETS_FOLDER . DIRECTORY_SEPARATOR . $id,DIRECTORY_SEPARATOR);
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $file) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $file)) return;
        $content = file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . $file);
        if (empty($content)) return;
        $data = @unserialize($content);
        if (!is_object($data) || !$data->path) return;
        $data->path = trim($data->path, DIRECTORY_SEPARATOR);
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path)) return;
        
        $d = @opendir(ROOT_DIR . DIRECTORY_SEPARATOR . $data->path);
        if (!$d) return;
        $images = array();
        $descriptions = array();
        $links = array();
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
            $image = str_replace(DIRECTORY_SEPARATOR, '/', $data->path . DIRECTORY_SEPARATOR . $f);
            $images []= $image;
            
            if (is_array($data->descriptions) && array_key_exists(Zira\Helper::urlencode($image), $data->descriptions)) {
                $descriptions []= $data->descriptions[Zira\Helper::urlencode($image)];
            }
            if (is_array($data->links) && array_key_exists(Zira\Helper::urlencode($image), $data->links)) {
                $links []= $data->links[Zira\Helper::urlencode($image)];
            }
        }
        closedir($d);

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;

        $data = array(
            'title' => $data->title ? Zira\Locale::t($data->title) : '',
            'images' => $images,
            'descriptions' => $descriptions,
            'links' => $links,
            'sidebar' => $is_sidebar
        );

        Zira\View::renderView($data, 'zira/widgets/carousel');
    }
}