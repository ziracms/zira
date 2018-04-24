<?php
/**
 * Zira project
 * page.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

use Zira\Models\Record;
use Dash\Dash;

class Page {
    const USER_TEXTAREA_HOOK = 'zira_user_textared_hook';

    const BREADCRUMBS_CLASS = 'breadcrumb';

    // View placeholders
    const VIEW_PLACEHOLDER_TITLE = 'title';
    const VIEW_PLACEHOLDER_IMAGE = 'image';
    const VIEW_PLACEHOLDER_CONTENT = 'content';
    const VIEW_PLACEHOLDER_DESCRIPTION = 'description';
    const VIEW_PLACEHOLDER_DATE = 'date';
    const VIEW_PLACEHOLDER_AUTHOR = 'author';
    const VIEW_PLACEHOLDER_RATING = 'rating';
    const VIEW_PLACEHOLDER_RECORDS = 'records';
    const VIEW_PLACEHOLDER_PAGINATION = 'pagination';
    const VIEW_PLACEHOLDER_SETTINGS = 'settings';
    const VIEW_PLACEHOLDER_CLASS = 'class';
    const VIEW_PLACEHOLDER_URL = 'url';
    const VIEW_PLACEHOLDER_ADMIN_ICONS = 'admin_icons';
    
    const VIEW_PLACEHOLDER_SLIDER_DATA = 'slider';
    const VIEW_PLACEHOLDER_GALLERY_DATA = 'gallery';
    const VIEW_PLACEHOLDER_VIDEO_DATA = 'videos';
    const VIEW_PLACEHOLDER_AUDIO_DATA = 'audio';
    const VIEW_PLACEHOLDER_FILES_DATA = 'files';
    const VIEW_PLACEHOLDER_COMMENTS_DATA = 'comments';
    const VIEW_PLACEHOLDER_CONTENT_VIEW_DATA = 'contentView';

    protected static $_view = 'page';
    protected static $_layout = null;
    protected static $_breadcrumbs = array();
    protected static $_record_id = null;
    protected static $_category_page_record_id = null;
    protected static $_record_url = null;
    protected static $_redirect_url = null;
    
    protected static $_category_childs = array();
    protected static $_placeholders_data = array();
    protected static $_records_preview_callbacks = array();
    protected static $_records_preview_data = array();

    public static function setView($view) {
        self::$_view = $view;
    }

    public static function getView() {
        return self::$_view;
    }

    public static function setLayout($layout) {
        self::$_layout = $layout;
    }

    public static function getLayout() {
        return self::$_layout;
    }
    
    public static function &getPlaceHoldersData() {
        return self::$_placeholders_data;
    }

    public static function addTitle($title) {
        //$title = Helper::html($title); //converted in tag generation
        $_title = View::getLayoutData(View::VAR_TITLE);
        if ($_title===null && Config::get('site_window_title') && Config::get('site_title')) {
            $_title = Locale::t(Config::get('site_title'));
        }
        if ($_title!==null) $title = $title . PAGE_TITLE_DELIMITER . $_title;
        View::setLayoutData(array(View::VAR_TITLE=>$title));
    }

    public static function setRedirectUrl($url) {
        self::$_redirect_url = $url;
    }

    public static function getRedirectUrl() {
        return self::$_redirect_url;
    }

    public static function setTitle($title) {
        //$title = Helper::html($title);  //converted in tag generation
        View::setLayoutData(array(View::VAR_TITLE=>$title));
    }

    public static function setKeywords($keywords) {
        //$keywords = Helper::html($keywords); //converted in addMeta
        View::addMeta(array('name'=>'keywords','content'=>$keywords));
        View::setKeywordsAdded(true);
    }

    public static function setDescription($description) {
        //$description = Helper::html($description); //converted in addMeta
        $description = str_replace("\r\n", ' ', $description);
        View::addMeta(array('name'=>'description','content'=>$description));
        View::setDescriptionAdded(true);
    }
    
    public static function registerRecordsPreviewHook($object, $method) {
        self::$_records_preview_callbacks []= array($object, $method);
    }
    
    public static function isRecordPreviewDataExists($record_id) {
        return array_key_exists($record_id, self::$_records_preview_data);
    }
    
    public static function addRecordPreviewData($record_id, $data, $view, $show_in_widgets = false, $module = 'zira') {
        if (!array_key_exists($record_id, self::$_records_preview_data)) self::$_records_preview_data[$record_id] = array();
        self::$_records_preview_data[$record_id][$module] = array(
            'data' => $data,
            'view' => $view,
            'show_in_widgets' => $show_in_widgets
        );
    }
    
    public static function getRecordPreviewData($record_id) {
        if (!array_key_exists($record_id, self::$_records_preview_data)) return false;
        return self::$_records_preview_data[$record_id];
    }

    public static function addOpenGraphTags($title, $description, $url = '', $image = null) {
        $description = str_replace("\r\n", ' ', $description);
        if ($image === null) $image = Config::get('site_logo');
        $tags = array(
            'og:site_name' => Config::get('site_title') ? Locale::t(Config::get('site_title')) : Locale::t(Config::get('site_name')),
            'og:type' => 'website',
            'og:title' => $title,
            'og:description' => $description,
            'og:url' => Helper::url($url, true, true),
            'og:image' => Helper::baseUrl($image, true, true)
        );
        foreach($tags as $property=>$content) {
            View::addMeta(array('property'=>$property,'content'=>$content));
        }
    }

    public static function addBreadcrumb($link, $title) {
        self::$_breadcrumbs [] = array('link'=>$link, 'title'=>$title);
    }

    public static function putBreadcrumb($link, $title) {
        for ($i=0; $i<count(self::$_breadcrumbs); $i++) {
            if (self::$_breadcrumbs[$i]['link'] == $link) {
                self::$_breadcrumbs[$i] = array('link'=>$link, 'title'=>$title);
                return;
            }
        }
        self::$_breadcrumbs [] = array('link'=>$link, 'title'=>$title);
    }

    public static function removeBreadcrumb($link) {
        for ($i=0; $i<count(self::$_breadcrumbs); $i++) {
            if (self::$_breadcrumbs[$i]['link'] == $link) {
                unset(self::$_breadcrumbs[$i]);
                return;
            }
        }
    }
    
    public static function removeBreadcrumbs() {
        self::$_breadcrumbs = array();
    }
    
    public static function resetBreadcrumbs() {
        self::removeBreadcrumbs();
        Page::addBreadcrumb('/', Locale::t('Home'));
    }

    public static function breadcrumbs() {
        if (!View::renderBreadcrumbsEnabled()) return '';
        if (!Router::getRequest() && Router::getModule()==DEFAULT_MODULE && Router::getController()==DEFAULT_CONTROLLER && Router::getAction()==DEFAULT_ACTION) return '';
        $added = array();
        $html = Helper::tag_open('ol',array('class'=>self::BREADCRUMBS_CLASS));
        foreach(self::$_breadcrumbs as $breadcrumb) {
            if (!empty($breadcrumb['link'])) {
                if (in_array($breadcrumb['link'], $added)) continue;
                $added []= $breadcrumb['link'];
            }
            if (empty($breadcrumb['link']) || $breadcrumb['link'] == Router::getRequest()) {
                $html .= Helper::tag('li', $breadcrumb['title'], array('class'=>'active'));
            } else {
                $html .= Helper::tag_open('li');
                $html .= Helper::tag('a', $breadcrumb['title'], array('href'=>Helper::url($breadcrumb['link'])));
                $html .= Helper::tag_close('li');
            }
        }
        $html .= Helper::tag_close('ol');
        return $html;
    }

    public static function allowPreview() {
        return Dash::isFrame() && Permission::check(Permission::TO_ACCESS_DASHBOARD);
    }

    public static function setSlider(array $images) {
        View::addSlider('slider', array(
            'auto' => true,
            'speed' => 500,
            'pause' => 8000,
            'captions' => true,
            'slideMargin' => 0,
            'adaptiveHeight' => false
        ));
        //View::addPlaceholderView(View::VAR_CONTENT_TOP, array('images'=>$images), 'zira/slider');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_SLIDER_DATA] = array('images'=>$images);
    }

    public static function setGallery(array $images, $access_allowed = true) {
        View::addLightbox();
        //View::addPlaceholderView(View::VAR_CONTENT, array('images'=>$images, 'access_allowed' => $access_allowed), 'zira/gallery');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_GALLERY_DATA] = array('images'=>$images, 'access_allowed' => $access_allowed);
    }
    
    public static function setFiles(array $files, $access_allowed = true) {
        //View::addPlaceholderView(View::VAR_CONTENT, array('files'=>$files, 'access_allowed' => $access_allowed), 'zira/files');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_FILES_DATA] = array('files'=>$files, 'access_allowed' => $access_allowed);
    }
    
    public static function setAudio(array $audio, $access_allowed = true) {
        $container_id = 'jplayer-container-audio';
        $player_id = 'jplayer-audio';
        $urls = array(); 
        $embeds = array();
        foreach($audio as $file) {
            if (!empty($file->path) || !empty($file->url)) $urls []= $file;
            else if (!empty($file->embed)) $embeds []= $file;
        }
        if (!empty($urls)) {
            View::addJPlayer($container_id, $player_id, $urls, 'audio');
        }
        //View::addPlaceholderView(View::VAR_CONTENT, array('urls'=>$urls, 'embeds'=>$embeds, 'access_allowed' => $access_allowed, 'container_id'=>$container_id, 'player_id'=>$player_id), 'zira/audio');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_AUDIO_DATA] = array('urls'=>$urls, 'embeds'=>$embeds, 'access_allowed' => $access_allowed, 'container_id'=>$container_id, 'player_id'=>$player_id);
    }
    
    public static function setVideo(array $video, $access_allowed = true, $poster = null) {
        $container_id = 'jplayer-container-video';
        $player_id = 'jplayer-video';
        $urls = array(); 
        $embeds = array();
        foreach($video as $file) {
            if (!empty($file->path) || !empty($file->url)) $urls []= $file;
            else if (!empty($file->embed)) $embeds []= $file;
        }
        if (!empty($urls)) {
            View::addJPlayer($container_id, $player_id, $urls, 'video', $poster);
        }
        //View::addPlaceholderView(View::VAR_CONTENT_TOP, array('urls'=>$urls, 'embeds'=>$embeds, 'access_allowed' => $access_allowed, 'poster' => $poster, 'container_id'=>$container_id, 'player_id'=>$player_id), 'zira/videos');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_VIDEO_DATA] = array('urls'=>$urls, 'embeds'=>$embeds, 'access_allowed' => $access_allowed, 'poster' => $poster, 'container_id'=>$container_id, 'player_id'=>$player_id);
    }

    public static function setComments($record, $preview = false) {
        $commenting_allowed = Config::get('comments_allowed',true);
        if (!Config::get('comment_anonymous',true) &&
            !User::isAuthorized()
        ) {
            $commenting_allowed = false;
        }
        if ($commenting_allowed) {
            $form = new Forms\Comment();
            $form->setValue('record_id', $record->id);
        } else {
            $form = null;
        }
        $limit = Config::get('comments_limit', 10);
        $comments = Models\Comment::getComments($record->id, $limit, 0, !$preview);
        $data = array(
            'record_id'=>$record->id,
            'form'=>$form,
            'comments'=>$comments,
            'limit'=>$limit,
            'page'=>0,
            'total'=>Models\Comment::countComments($record->id, !$preview),
            'commenting_allowed'=>$commenting_allowed
        );
        //View::addPlaceholderView(View::VAR_CONTENT, $data, 'zira/comments');
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_COMMENTS_DATA] = $data;
        View::addParser();
    }
    
    public static function setContentView(array $data, $view, $module = 'zira') {
        if (!array_key_exists(self::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA, self::$_placeholders_data)) {
            self::$_placeholders_data[self::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA] = array();
        }
        if (!array_key_exists($module, self::$_placeholders_data[self::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA])) {
            self::$_placeholders_data[self::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA][$module] = array();
        }
        self::$_placeholders_data[self::VIEW_PLACEHOLDER_CONTENT_VIEW_DATA][$module] = array('data'=>$data, 'view' => $view);
    }

    public static function encodeURL($url) {
        return Helper::urlencode($url);
    }

    public static function generateCategoryUrl($category_name) {
        return self::encodeURL($category_name);
    }

    public static function generateRecordUrl($category_name, $record_name) {
        if (empty($category_name)) {
            if ($record_name == Config::get('home_record_name')) return '';
            return self::encodeURL($record_name);
        }
        else return self::encodeURL($category_name) . '/' . self::encodeURL($record_name);
    }

    public static function getRecordId() {
        return self::$_record_id;
    }
    
    public static function getCategoryPageRecordId() {
        return self::$_category_page_record_id;
    }

    public static function getRecordUrl() {
        return self::$_record_url;
    }

    public static function setRecordId($record_id) {
        self::$_record_id = (int)$record_id;
    }
    
    public static function setCategoryPageRecordId($record_id) {
        self::$_category_page_record_id = $record_id;
    }

    public static function setRecordUrl($url) {
        self::$_record_url = $url;
    }

    public static function createRecordThumb($src_path, $category_id, $record_id, $gallery=false, $slider=false) {
        if ($category_id==Category::ROOT_CATEGORY_ID) {
            $savedir = THUMBS_DIR;
        } else {
            $savedir = THUMBS_DIR . DIRECTORY_SEPARATOR . 'cat'. $category_id;
        }
        if ($gallery) $savedir .= DIRECTORY_SEPARATOR . 'gal'.$record_id;
        else if ($slider) $savedir .= DIRECTORY_SEPARATOR . 'sli'.$record_id;
        $save_path = File::getAbsolutePath($savedir);
        $ext = 'thumb';
        $p = strrpos($src_path, '.');
        if ($p!==false) $ext = substr($src_path, $p+1);
        do {
            if (!$gallery && !$slider) {
                $name = 'rec' . $record_id . '.' . uniqid() . '.' . $ext;
            } else {
                $name = 'img' . uniqid() . '.' . $ext;
            }
        } while(file_exists($save_path . DIRECTORY_SEPARATOR . $name));
        if (file_exists($src_path) && Image::createThumb($src_path, $save_path . DIRECTORY_SEPARATOR . $name, Config::get('thumbs_width'), Config::get('thumbs_height'))) {
            return UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $savedir) . '/' . $name;
        } else {
            return false;
        }
    }
    
    public static function runRecordsHook($records, $is_widget = false) {
        foreach(self::$_records_preview_callbacks as $callback) {
            try {
                call_user_func($callback, $records, $is_widget);
            } catch (Exception $e) {
                // ignore
            }
        }
    }

    public static function getRecords($category, $front_page = false, $limit = null, $last_id = null, $includeChilds = true, array $childs = null, $page = 1, $is_widget = false) {
        if ($limit === null) $limit = Config::get('records_limit', 10);

        $category_ids = array($category->id);
        if ($includeChilds) {
            if ($childs === null) $childs = Category::getChilds($category);
            foreach ($childs as $child) {
                $category_ids [] = $child->id;
            }
        }

        if ($includeChilds && count($category_ids)>1) {
            $records = self::getCategoriesRecordsList($category_ids, $front_page, $limit, $last_id, $page);
        } else {
            $records = self::getCategoryRecordsList($category_ids[0], $front_page, $limit, $last_id, $page);
        }
        
        self::runRecordsHook($records, $is_widget);

        return $records;
    }
    
    public static function getWidgetRecords($category, $front_page = false, $limit = null, $last_id = null, $includeChilds = true, array $childs = null, $page = 1) {
        return self::getRecords($category, $front_page, $limit, $last_id, $includeChilds, $childs, $page, true);
    }
    
    public static function getRecordsCount($category, $front_page = false, $includeChilds = true, array $childs = null) {
        $category_ids = array($category->id);
        if ($includeChilds) {
            if ($childs === null) $childs = Category::getChilds($category);
            foreach ($childs as $child) {
                $category_ids [] = $child->id;
            }
        }
        
        if ($includeChilds && count($category_ids)>1) {
            return self::getCategoriesRecordsCount($category_ids, $front_page);
        } else {
            return self::getCategoryRecordsCount($category_ids[0], $front_page);
        }
    }

    public static function getCategoryRecordsList($category_id, $front_page = false, $limit = null, $last_id = null, $page = 1) {
        if ($limit === null) $limit = Config::get('records_limit', 10);

        if ($page < 1) $page = 1;
        $offset = $limit * ($page - 1);
        
        $query = Record::getCollection()
                        ->select('id')
                        ->where('category_id', '=', $category_id)
                        ->and_where('language', '=', Locale::getLanguage())
                        ->and_where('published', '=', Record::STATUS_PUBLISHED)
                        ;
        if ($front_page) {
            $query->and_where('front_page','=',Record::STATUS_FRONT_PAGE);
        }
        if ($last_id!==null) {
            $query->and_where('id', '<', $last_id);
        }
        $query->order_by('id', 'desc');
        $query->limit($limit, $offset);

        $rows = $query->get();

        if (!$rows) return array();
        
        $query = Record::getCollection()
                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
                        ->join(Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ->join(Models\User::getClass(), array('author_username'=>'username', 'author_firstname'=>'firstname', 'author_secondname'=>'secondname'))
                        ;
        
        $record_ids = array();
        foreach($rows as $index=>$row) {
            $record_ids []= $row->id;
        }
        $query->where('id','in',$record_ids);

        $query->order_by('id', 'desc');
        
        return $query->get();
    }
    
    public static function getCategoryRecordsCount($category_id, $front_page = false) {
        $query = Record::getCollection()
                        ->count()
                        ->join(Models\Category::getClass())
                        ->join(Models\User::getClass())
                        ;

        $query->where('category_id', '=', $category_id);
        $query->and_where('language', '=', Locale::getLanguage());
        $query->and_where('published', '=', Record::STATUS_PUBLISHED);
        if ($front_page) {
            $query->and_where('front_page','=',Record::STATUS_FRONT_PAGE);
        }
        
        return $query->get('co');
    }

    public static function getCategoriesRecordsList(array $category_ids, $front_page = false, $limit = null, $last_id = null, $page = 1) {
        if ($limit === null) $limit = Config::get('records_limit', 10);

        if ($page < 1) $page = 1;
        $offset = $limit * ($page - 1);
        
        $query = Record::getCollection();
        foreach($category_ids as $index=>$category_id) {
            if ($index>0) {
                $query->union();
            }
            $query->open_query();
            $query->select('id');
            $query->where('category_id', '=', $category_id);
            $query->and_where('language', '=', Locale::getLanguage());
            $query->and_where('published', '=', Record::STATUS_PUBLISHED);
            if ($front_page) {
                $query->and_where('front_page','=',Record::STATUS_FRONT_PAGE);
            }
            if ($last_id!==null) {
                $query->and_where('id', '<', $last_id);
            }
            $query->order_by('id', 'desc');
            $query->limit($limit * $page);
            $query->close_query();
        }
        $query->merge();
        $query->order_by('id', 'desc');
        $query->limit($limit, $offset);

        $rows = $query->get();

        if (!$rows) return array();

        $query = Record::getCollection()
                        ->select('id', 'name','author_id','title','description','thumb','creation_date','rating','comments')
                        ->join(Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ->join(Models\User::getClass(), array('author_username'=>'username', 'author_firstname'=>'firstname', 'author_secondname'=>'secondname'))
                        ;

        $record_ids = array();
        foreach($rows as $index=>$row) {
            $record_ids []= $row->id;
        }
        $query->where('id','in',$record_ids);

        $query->order_by('id', 'desc');

        return $query->get();
    }
    
    public static function getCategoriesRecordsCount(array $category_ids, $front_page = false) {
        $use_union = true; // seems faster
        if (!$use_union) {
            $query = Record::getCollection();
            $query->count();
            $query->join(Models\Category::getClass());
            $query->join(Models\User::getClass());             
            $query->where('category_id', 'in', $category_ids);
            $query->and_where('language', '=', Locale::getLanguage());
            $query->and_where('published', '=', Record::STATUS_PUBLISHED);
            if ($front_page) {
                $query->and_where('front_page','=',Record::STATUS_FRONT_PAGE);
            }
            return $query->get('co');
        } else {
            $query = Record::getCollection();
            foreach ($category_ids as $index=>$category_id) {
                if ($index>0) $query->union();
                $query->count();
                $query->join(Models\Category::getClass());
                $query->join(Models\User::getClass());             
                $query->where('category_id', '=', $category_id);
                $query->and_where('language', '=', Locale::getLanguage());
                $query->and_where('published', '=', Record::STATUS_PUBLISHED);
                if ($front_page) {
                    $query->and_where('front_page','=',Record::STATUS_FRONT_PAGE);
                }
            }
            $rows = $query->get();
            $co = 0;
            foreach($rows as $row) {
                $co += $row->co;
            }
            return $co;
        }
        
    }
    
    public static function getRecordSlides($record_id) {
        return Models\Slide::getCollection()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get();
    }
    
    public static function getRecordSlidesCount($record_id) {
        return Models\Slide::getCollection()
                            ->count()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get('co');
    }
    
    public static function getRecordImages($record_id) {
        return Models\Image::getCollection()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get();
    }
    
    public static function getRecordImagesCount($record_id) {
        return Models\Image::getCollection()
                            ->count()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get('co');
    }
    
    public static function getRecordFiles($record_id) {
        return Models\File::getCollection()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get();
    }
    
    public static function getRecordFilesCount($record_id) {
        return Models\File::getCollection()
                            ->count()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get('co');
    }
    
    public static function getRecordAudio($record_id) {
        return Models\Audio::getCollection()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get();
    }
    
    public static function getRecordAudioCount($record_id) {
        return Models\Audio::getCollection()
                            ->count()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get('co');
    }
    
    public static function getRecordVideos($record_id) {
        return Models\Video::getCollection()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get();
    }
    
    public static function getRecordVideosCount($record_id) {
        return Models\Video::getCollection()
                            ->count()
                            ->where('record_id', '=', $record_id)
                            ->order_by('id', 'asc')
                            ->get('co');
    }
    
    public static function setPlaceholdersData() {
        if (!empty(self::$_placeholders_data)) {
            foreach(self::$_placeholders_data as $_var=>$_data) {
                if (array_key_exists($_var, View::$data)) continue;
                View::$data[$_var] = $_data;
            }
        }
    }
    
    public static function renderContentView(array $contentView, $module = null) {
        if ($module===null) {
            foreach($contentView as $_module=>$_contentView) {
                if ($_module!='zira' && !in_array($_module, Config::get('modules'))) continue;
                if (isset($_contentView['data']) && isset($_contentView['view'])) {
                    View::renderView($_contentView['data'], $_contentView['view']);
                }
            }
        } else if (array_key_exists($module, $contentView)) {
            View::renderView($contentView[$module]['data'], $contentView[$module]['view']);
        }
    }
    
    public static function renderRecordPreview($record_id, $is_widget = false, $module = null) {
        $data = self::getRecordPreviewData($record_id);
        if (!$data) return;
        if ($module === null) {
            foreach ($data as $_module => $_data) {
                if ($_module!='zira' && !in_array($_module, Config::get('modules'))) continue;
                if (!isset($_data['data']) || !isset($_data['view']) || !isset($_data['show_in_widgets'])) continue;
                if (!$_data['show_in_widgets'] && $is_widget) continue;
                View::renderView($_data['data'], $_data['view']);
            }
        } else {
            if (!array_key_exists($module, $data)) return;
            if (!isset($data[$module]['data']) || !isset($data[$module]['view']) || !isset($data[$module]['show_in_widgets'])) return;
            if (!$data[$module]['show_in_widgets'] && $is_widget) return;
            View::renderView($data[$module]['data'], $data[$module]['view']);
        }
    }
    
    public static function renderRecordWidgetPreview($record_id, $module = null) {
        self::renderRecordPreview($record_id, true, $module);
    }

    public static function render(array $data = null) {
        if ($data === null) $data = array();
        if (View::isAjax()) {
            if (!isset($data[self::VIEW_PLACEHOLDER_CONTENT]) || !($data[self::VIEW_PLACEHOLDER_CONTENT] instanceof Form\Factory)) {
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'message'=>$data[self::VIEW_PLACEHOLDER_CONTENT]->getMessage(),
                    'error'=>$data[self::VIEW_PLACEHOLDER_CONTENT]->getError()
                ));
            }
            return;
        }
        View::registerRenderHook(get_called_class(), 'setPlaceholdersData');
        View::render($data, self::$_view, self::$_layout);
    }
}