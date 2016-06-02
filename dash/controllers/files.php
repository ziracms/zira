<?php
/**
 * Zira project.
 * file.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Files extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $class = (string)Zira\Request::post('class');
        $window = new Dash\Windows\Selector();
        if ($class != $window->getJSClassName()) {
            $window = new Dash\Windows\Files();
        }
        return new Dash\Models\Files($window);
    }

    /**
    public function upload() {
        if (Zira\Request::isPost()) {
            $response = (new Dash\Windows\Files())->upload();
            Zira\Page::render($response);
        }
    }
    **/

    public function xhrupload() {
        if (Zira\Request::isPost()) {
            $files = Zira\Request::file('files');
            $dir = Zira\Request::post('dirroot');

            $form = new Dash\Forms\Upload();
            Zira\Request::setPost($form->getFieldName('dirroot'), $dir);
            Zira\Request::setFile($form->getFieldName('files'), $files);

            $response = $this->getWindowModel()->upload();
            Zira\Page::render($response);
        }
    }

    public function xhruploadsrc() {
        if (Zira\Request::isPost()) {
            $url = trim(urldecode(trim((string)Zira\Request::post('url'))),'/');
            $dir = (string)Zira\Request::post('dirroot');

            if (strpos($url,'http')!==0) $url = 'http://'.$url;
            $name = preg_replace('/^.+\/([^\/]+?)([\?].*)?$/','$1',$url);

            if (substr($name,-4)!='.php') {
                $path = Zira\File::getAbsolutePath(TMP_DIR) . DIRECTORY_SEPARATOR . $name;
                $contents = file_get_contents($url);
                if (!empty($contents) && file_put_contents($path, $contents)) {
                    $file=Zira\File::getFileArray($path);

                    $form = new Dash\Forms\Upload();
                    Zira\Request::setPost($form->getFieldName('dirroot'), $dir);
                    Zira\Request::setFile($form->getFieldName('files'), $file);

                    $response = $this->getWindowModel()->upload();
                } else {
                    $response = array('error'=>Zira\Locale::t('Load failed'));
                }

                if (file_exists($path)) @unlink($path);
            } else {
                $response = array('error'=>Zira\Locale::t('Permission denied'));
            }

            Zira\Page::render($response);
        }
    }

    public function mkdir() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $name = trim(Zira\Request::post('name'));
            $response = $this->getWindowModel()->mkdir($root, $name);
            Zira\Page::render($response);
        }
    }

    public function textfile() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $name = trim(Zira\Request::post('name'));
            $response = $this->getWindowModel()->createTextFile($root, $name);
            Zira\Page::render($response);
        }
    }

    public function htmlfile() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $name = trim(Zira\Request::post('name'));
            $response = $this->getWindowModel()->createHTMLFile($root, $name);
            Zira\Page::render($response);
        }
    }

    public function rename() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $name = trim(Zira\Request::post('name'));
            $response = $this->getWindowModel()->rename($file, $name);
            Zira\Page::render($response);
        }
    }

    public function copy() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $dir = trim(Zira\Request::post('path'));
            $response = $this->getWindowModel()->copy($file, $dir);
            Zira\Page::render($response);
        }
    }

    public function move() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $dir = trim(Zira\Request::post('path'));
            $response = $this->getWindowModel()->move($file, $dir);
            Zira\Page::render($response);
        }
    }

    public function pack() {
        if (Zira\Request::isPost()) {
            $root = (string)Zira\Request::post('dirroot');
            $items = Zira\Request::post('files');
            $name = (string)Zira\Request::post('name');
            $response = $this->getWindowModel()->pack($name, $items, $root);
            Zira\Page::render($response);
        }
    }

    public function unpack() {
        if (Zira\Request::isPost()) {
            $root = (string)Zira\Request::post('dirroot');
            $file = (string)Zira\Request::post('file');
            $response = $this->getWindowModel()->unpack($file, $root);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $root = (string)Zira\Request::post('dirroot');
            $file = (string)Zira\Request::post('file');
            $response = $this->getWindowModel()->info($file, $root);
            Zira\Page::render($response);
        }
    }
}