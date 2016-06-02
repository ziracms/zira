<?php
/**
 * Zira project.
 * image.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Image extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Image();
        return new Dash\Models\Image($window);
    }

    public function open() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');

            $response = $this->getWindowModel()->open($file);
            Zira\Page::render($response);
        }
    }

    public function close() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');

            $response = $this->getWindowModel()->close($file);
            Zira\Page::render($response);
        }
    }

    public function width() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $width = Zira\Request::post('width');

            $response = $this->getWindowModel()->changeWidth($file, $width);
            Zira\Page::render($response);
        }
    }

    public function height() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $height = Zira\Request::post('height');

            $response = $this->getWindowModel()->changeHeight($file, $height);
            Zira\Page::render($response);
        }
    }

    public function cropwidth() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $width = Zira\Request::post('width');

            $response = $this->getWindowModel()->cropWidth($file, $width);
            Zira\Page::render($response);
        }
    }

    public function cropheight() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $height = Zira\Request::post('height');

            $response = $this->getWindowModel()->cropHeight($file, $height);
            Zira\Page::render($response);
        }
    }

    public function crop() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $width = Zira\Request::post('width');
            $height = Zira\Request::post('height');
            $left = Zira\Request::post('left');
            $top = Zira\Request::post('top');

            $response = $this->getWindowModel()->crop($file, $width, $height, $left, $top);
            Zira\Page::render($response);
        }
    }

    public function save() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');

            $response = $this->getWindowModel()->saveTmpImage($file);
            Zira\Page::render($response);
        }
    }

    public function saveas() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');
            $name = Zira\Request::post('name');

            $response = $this->getWindowModel()->saveTmpImageAs($file, $name);
            Zira\Page::render($response);
        }
    }

    public function watermark() {
        if (Zira\Request::isPost()) {
            $file = Zira\Request::post('file');

            $response = $this->getWindowModel()->watermark($file);
            Zira\Page::render($response);
        }
    }
}