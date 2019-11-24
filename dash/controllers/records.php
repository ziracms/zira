<?php
/**
 * Zira project.
 * record.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Records extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Records();
        return new Dash\Models\Records($window);
    }

    protected function getImagesModel() {
        $window = new Dash\Windows\Recordimages();
        return new Dash\Models\Recordimages($window);
    }

    protected function getSlidesModel() {
        $window = new Dash\Windows\Recordslides();
        return new Dash\Models\Recordslides($window);
    }
    
    protected function getFilesModel() {
        $window = new Dash\Windows\Recordfiles();
        return new Dash\Models\Recordfiles($window);
    }
    
    protected function getAudioModel() {
        $window = new Dash\Windows\Recordaudio();
        return new Dash\Models\Recordaudio($window);
    }
    
    protected function getVideoModel() {
        $window = new Dash\Windows\Recordvideos();
        return new Dash\Models\Recordvideos($window);
    }

    protected function getEditorModel() {
        $class = (string)Zira\Request::post('class');
        $window = new Dash\Windows\Recordhtml();
        if ($window->getJSClassName() != $class) {
            $window = new Dash\Windows\Recordtext();
            $model = new Dash\Models\Recordtext($window);
        } else {
            $model = new Dash\Models\Recordhtml($window);
        }
        return $model;
    }

    public function description() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->setRecordDescription($id, $description);
            Zira\Page::render($response);
        }
    }

    public function image() {
        if (Zira\Request::isPost()) {
            $image = Zira\Request::post('image');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->setRecordImage($id, $image);
            Zira\Page::render($response);
        }
    }
    
    public function rethumb() {
        if (Zira\Request::isPost()) {
            $ids = Zira\Request::post('items');
            $response = $this->getWindowModel()->updateRecordImages($ids);
            Zira\Page::render($response);
        }
    }

    public function copy() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->copyRecord($root, $id);
            Zira\Page::render($response);
        }
    }
    
    public function copies() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $ids = Zira\Request::post('items');
            $response = $this->getWindowModel()->copyRecords($root, $ids);
            Zira\Page::render($response);
        }
    }

    public function move() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->moveRecord($root, $id);
            Zira\Page::render($response);
        }
    }
    
    public function moves() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $ids = Zira\Request::post('items');
            $response = $this->getWindowModel()->moveRecords($root, $ids);
            Zira\Page::render($response);
        }
    }

    public function publish() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->publishRecord($id);
            Zira\Page::render($response);
        }
    }

    public function frontpage() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->publishRecordOnFrontPage($id);
            Zira\Page::render($response);
        }
    }

    public function addimage() {
        if (Zira\Request::isPost()) {
            $images = Zira\Request::post('images');
            $id = Zira\Request::post('item');
            $response = $this->getImagesModel()->addRecordImages($id, $images);
            Zira\Page::render($response);
        }
    }
    
    public function addimages() {
        if (Zira\Request::isPost()) {
            $folder = Zira\Request::post('folder');
            $id = Zira\Request::post('item');
            $response = $this->getImagesModel()->addFolderImages($id, $folder);
            Zira\Page::render($response);
        }
    }

    public function imagedesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getImagesModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }
    
    public function imageupdate() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $images = Zira\Request::post('images');
            $response = $this->getImagesModel()->updateThumbs($id, $images);
            Zira\Page::render($response);
        }
    }

    public function addslide() {
        if (Zira\Request::isPost()) {
            $images = Zira\Request::post('images');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->addRecordSlides($id, $images);
            Zira\Page::render($response);
        }
    }
    
    public function addslides() {
        if (Zira\Request::isPost()) {
            $folder = Zira\Request::post('folder');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->addFolderSlides($id, $folder);
            Zira\Page::render($response);
        }
    }

    public function slidedesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }
    
    public function slidelink() {
        if (Zira\Request::isPost()) {
            $link = Zira\Request::post('link');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->saveLink($id, $link);
            Zira\Page::render($response);
        }
    }
    
    public function addfile() {
        if (Zira\Request::isPost()) {
            $files = Zira\Request::post('files');
            $url = Zira\Request::post('url');
            $id = Zira\Request::post('item');
            $response = $this->getFilesModel()->addRecordFiles($id, $files, $url);
            Zira\Page::render($response);
        }
    }
    
    public function addfiles() {
        if (Zira\Request::isPost()) {
            $folder = Zira\Request::post('folder');
            $id = Zira\Request::post('item');
            $response = $this->getFilesModel()->addFolderFiles($id, $folder);
            Zira\Page::render($response);
        }
    }

    public function filedesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getFilesModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }
    
    public function addaudio() {
        if (Zira\Request::isPost()) {
            $files = Zira\Request::post('files');
            $url = Zira\Request::post('url');
            $code = Zira\Request::post('code');
            $id = Zira\Request::post('item');
            $response = $this->getAudioModel()->addRecordAudio($id, $files, $url, $code);
            Zira\Page::render($response);
        }
    }
    
    public function addaudios() {
        if (Zira\Request::isPost()) {
            $folder = Zira\Request::post('folder');
            $id = Zira\Request::post('item');
            $response = $this->getAudioModel()->addFolderAudio($id, $folder);
            Zira\Page::render($response);
        }
    }
    
    public function editaudio() {
        if (Zira\Request::isPost()) {
            $url = Zira\Request::post('url');
            $code = Zira\Request::post('code');
            $id = Zira\Request::post('item');
            $response = $this->getAudioModel()->editRecordAudio($id, $url, $code);
            Zira\Page::render($response);
        }
    }

    public function audiodesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getAudioModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }
    
    public function addvideo() {
        if (Zira\Request::isPost()) {
            $files = Zira\Request::post('files');
            $url = Zira\Request::post('url');
            $code = Zira\Request::post('code');
            $id = Zira\Request::post('item');
            $response = $this->getVideoModel()->addRecordVideos($id, $files, $url, $code);
            Zira\Page::render($response);
        }
    }
    
    public function addvideos() {
        if (Zira\Request::isPost()) {
            $folder = Zira\Request::post('folder');
            $id = Zira\Request::post('item');
            $response = $this->getVideoModel()->addFolderVideos($id, $folder);
            Zira\Page::render($response);
        }
    }
    
    public function editvideo() {
        if (Zira\Request::isPost()) {
            $url = Zira\Request::post('url');
            $code = Zira\Request::post('code');
            $id = Zira\Request::post('item');
            $response = $this->getVideoModel()->editRecordVideo($id, $url, $code);
            Zira\Page::render($response);
        }
    }

    public function videodesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getVideoModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }

    public function savedraft() {
        if (Zira\Request::isPost()) {
            $content = Zira\Request::post('content');
            $id = Zira\Request::post('item');
            $response = $this->getEditorModel()->saveDraft($id, $content);
            Zira\Page::render($response);
        }
    }

    public function draft() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getEditorModel()->loadDraft($id);
            Zira\Page::render($response);
        }
    }

    public function widget() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->createCategoryWidget($id);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->info($id);
            Zira\Page::render($response);
        }
    }

    public function autocompletetag() {
        if (Zira\Request::isPost()) {
            $text = Zira\Request::post('text');
            $response = $this->getWindowModel()->autoCompleteTag($text);
            Zira\Page::render($response);
        }
    }
}