<?php
/**
 * Zira project.
 * page.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Content;

use Zira;

class Page extends Zira\Page {
    public static function content($param, $preview = false) {
        if (empty($param)) return;

        if (Zira\Category::current()) $category_id = Zira\Category::current()->id;
        else $category_id = Zira\Category::ROOT_CATEGORY_ID;

        // record options
        if (!Zira\Category::current()) {
            $slider_enabled = Zira\Config::get('slider_enabled', 1);
            $gallery_enabled = Zira\Config::get('gallery_enabled', 1);
            $files_enabled = Zira\Config::get('files_enabled', 1);
            $audio_enabled = Zira\Config::get('audio_enabled', 1);
            $video_enabled = Zira\Config::get('video_enabled', 1);
            $rating_enabled = Zira\Config::get('rating_enabled', 0);
            $display_author = Zira\Config::get('display_author', 0);
            $display_date = Zira\Config::get('display_date', 0);
        } else {
            $slider_enabled = Zira\Category::current()->slider_enabled!==null ? Zira\Category::current()->slider_enabled : Zira\Config::get('slider_enabled', 1);
            $gallery_enabled = Zira\Category::current()->gallery_enabled!==null ? Zira\Category::current()->gallery_enabled : Zira\Config::get('gallery_enabled', 1);
            $files_enabled = Zira\Category::current()->files_enabled!==null ? Zira\Category::current()->files_enabled : Zira\Config::get('files_enabled', 1);
            $audio_enabled = Zira\Category::current()->audio_enabled!==null ? Zira\Category::current()->audio_enabled : Zira\Config::get('audio_enabled', 1);
            $video_enabled = Zira\Category::current()->video_enabled!==null ? Zira\Category::current()->video_enabled : Zira\Config::get('video_enabled', 1); 
            $rating_enabled = Zira\Category::current()->rating_enabled!==null ? Zira\Category::current()->rating_enabled : Zira\Config::get('rating_enabled', 0);
            $display_author = Zira\Category::current()->display_author!==null ? Zira\Category::current()->display_author : Zira\Config::get('display_author', 0);
            $display_date = Zira\Category::current()->display_date!==null ? Zira\Category::current()->display_date : Zira\Config::get('display_date', 0);
        }

        $query = Zira\Models\Record::getCollection()
                        ->select(Zira\Models\Record::getFields())
                        ->where('category_id', '=', $category_id)
                        ->and_where('language', '=', Zira\Locale::getLanguage())
                        ->and_where('name', '=', $param)
                        ;

        if (!$preview) {
            $query->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED);
        }

        if ($display_author) {
            $query->join(Zira\Models\User::getClass(), array('author_username'=>'username', 'author_firstname'=>'firstname', 'author_secondname'=>'secondname'));
        }

        $row = $query->get(0);

        if (!$row) {
            Zira\Response::notFound();
        }

        $home_page_name = Zira\Config::get('home_record_name');
        if ($home_page_name && $row->name == $home_page_name && $row->category_id == Zira\Category::ROOT_CATEGORY_ID) {
            Zira\Response::redirect('/', true);
        }

        static::setRecordId($row->id);
        if (!Zira\Category::current()) {
            static::setRedirectUrl(static::generateRecordUrl(null, $row->name));
        } else {
            static::setRedirectUrl(static::generateRecordUrl(Zira\Category::current()->name, $row->name));
        }

        if (Zira\Category::current()) static::$_record_url = static::generateRecordUrl(Zira\Category::current()->name, $row->name);
        else static::$_record_url = static::generateRecordUrl(null, $row->name);

        $comments_enabled = Zira\Config::get('comments_enabled', 1);
        if (Zira\Category::current() && Zira\Category::current()->comments_enabled!==null) $comments_enabled = Zira\Category::current()->comments_enabled && $comments_enabled;
        if ($row->comments_enabled !== null) $comments_enabled = $row->comments_enabled && $comments_enabled;
        
        if (!$row->slides_count) $slider_enabled = false;
        if (!$row->images_count) $gallery_enabled = false;
        if (!$row->files_count) $files_enabled = false;
        if (!$row->audio_count) $audio_enabled = false;
        if (!$row->video_count) $video_enabled = false;
        
        // checking permission
        if (((Zira\Category::current() && Zira\Category::current()->access_check) || $row->access_check) &&
            !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORD)
        ) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.static::$_record_url, true);
            } else {
                Zira\Response::forbidden();
            }
        }

        // adding meta tags
        if ($row->meta_title) $meta_title = $row->meta_title;
        else $meta_title = $row->title;
        if ($row->meta_description) $meta_description = $row->meta_description;
        else $meta_description = $row->description;
        if ($row->image) $image = $row->image;
        else $image = null;

        static::addTitle($meta_title);
        static::setKeywords($row->meta_keywords);
        static::setDescription($meta_description);
        static::addOpenGraphTags($meta_title, $meta_description, static::$_record_url, $image);
        
        // adding canonical url
        if (Zira\Category::current()) {
            $canonical_url = static::generateRecordUrl(Zira\Category::current()->name, $row->name);
        } else {
            $canonical_url = static::generateRecordUrl(null, $row->name);
        }
        $canonical_link = Zira\Helper::tag_short('link', array('rel'=>'canonical', 'href'=>Zira\Helper::baseUrl($canonical_url, true, true)));
        Zira\View::addHTML($canonical_link, Zira\View::VAR_HEAD_TOP);
        
        // checking permission for gallery, files, audio & video
        if (Zira\Category::current()) {
            $category_gallery_check = Zira\Category::current()->gallery_check;
            $category_files_check = Zira\Category::current()->files_check;
            $category_audio_check = Zira\Category::current()->audio_check;
            $category_video_check = Zira\Category::current()->video_check;
        } else {
            $category_gallery_check = false;
            $category_files_check = false;
            $category_audio_check = false;
            $category_video_check = false;
        }
        
        if (($row->gallery_check || $category_gallery_check || Zira\Config::get('gallery_check')) &&
           !Zira\Permission::check(Zira\Permission::TO_VIEW_GALLERY)
        ) {
            $access_gallery = false;
        } else {
            $access_gallery = true;
        }
        if (($row->files_check || $category_files_check || Zira\Config::get('files_check')) && 
            !Zira\Permission::check(Zira\Permission::TO_DOWNLOAD_FILES)
        ) {
            $access_files = false;
        } else {
            $access_files = true;
        }
        if (($row->audio_check || $category_audio_check || Zira\Config::get('audio_check')) && 
           !Zira\Permission::check(Zira\Permission::TO_LISTEN_AUDIO)
        ) {
            $access_audio = false;
        } else {
            $access_audio = true;
        }
        if (($row->video_check || $category_video_check || Zira\Config::get('video_check')) && 
           !Zira\Permission::check(Zira\Permission::TO_VIEW_VIDEO)
        ) {
            $access_video = false;
        } else {
            $access_video = true;
        }
                    
        if ($display_author) {
            $author = Zira\User::generateUserProfileLink($row->author_id, $row->author_firstname, $row->author_secondname, $row->author_username, 'author');
        } else {
            $author = null;
        }

        if ($slider_enabled) {
            $slides = static::getRecordSlides($row->id);
            $slides_co = count($slides);
        } else {
            $slides = array();
            $slides_co = 0;
        }

        $images_limit = intval(Zira\Config::get('gallery_limit', 0));
        if ($gallery_enabled && $access_gallery) {
            $images_co = static::getRecordImagesCount($row->id);
            if ($images_co>0) {
                $images = static::getRecordImages($row->id, $images_limit);
            } else {
                $images = array();
            }
        } else if ($gallery_enabled && !$access_gallery) {
            $images = array();
            $images_co = static::getRecordImagesCount($row->id);
        } else {
            $images = array();
            $images_co = 0;
        }

        if ($files_enabled && $access_files) {
            $files = static::getRecordFiles($row->id);
            $files_co = count($files);
        } else if ($files_enabled && !$access_files) {
            $files = array();
            $files_co = static::getRecordFilesCount($row->id);
        } else {
            $files = array();
            $files_co = 0;
        }

        if ($audio_enabled && $access_audio) {
            $audio = static::getRecordAudio($row->id);
            $audio_co = count($audio);
        } else if ($audio_enabled && !$access_audio) {
            $audio = array();
            $audio_co = static::getRecordAudioCount($row->id);
        } else {
            $audio = array();
            $audio_co = 0;
        }

        if ($video_enabled && $access_video) {
            $video = static::getRecordVideos($row->id);
            $video_co = count($video);
        } else if ($video_enabled && !$access_video) {
            $video = array();
            $video_co = static::getRecordVideosCount($row->id);
        } else {
            $video = array();
            $video_co = 0;
        }

        if ($slides_co > 0) static::setSlider($slides);
        if ($images_co > 0) static::setGallery($images, $access_gallery, $images_limit, $images_co, $row->id);
        if ($audio_co > 0) static::setAudio($audio, $access_audio);
        if ($video_co > 0) static::setVideo($video, $access_video, $row->image);
        if ($files_co > 0) static::setFiles($files, $access_files);
                    
        if ($comments_enabled) static::setComments($row, $preview);

        Zira\View::addParser();

        $admin_icons = null;
        if (!$preview && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) && Zira\Permission::check(Zira\Permission::TO_EDIT_RECORDS)) {
            $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
            $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-bookmark category', 'data-item'=>Zira\Category::current() ? '/'.Zira\Category::current()->name : ''));
            $admin_icons .= '&nbsp;';
            $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-file record', 'data-item'=>$row->id));
            $admin_icons .= Zira\Helper::tag_close('div');
        }

        static::render(array(
            static::VIEW_PLACEHOLDER_TITLE => $row->title,
            static::VIEW_PLACEHOLDER_IMAGE => (empty($slides) || !$slider_enabled) && (empty($video) || !$video_enabled) ? $row->image : null,
            static::VIEW_PLACEHOLDER_CONTENT => $row->content,
            static::VIEW_PLACEHOLDER_DATE => $display_date ? $row->modified_date : null,
            static::VIEW_PLACEHOLDER_AUTHOR => $author,
            static::VIEW_PLACEHOLDER_RATING => $rating_enabled ? $row->rating : null,
            static::VIEW_PLACEHOLDER_URL => static::$_record_url,
            static::VIEW_PLACEHOLDER_CLASS => 'parse-content',
            static::VIEW_PLACEHOLDER_ADMIN_ICONS => $admin_icons
        ));
    }
}