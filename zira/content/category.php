<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Content;

use Zira;

class Category extends Zira\Page {
    public static function content($page = null, $last_id = null, $is_ajax = false) {
        if (!Zira\Category::current()) return;
        static::setRedirectUrl(Zira\Category::current()->name);
        // checking permission
        if (Zira\Category::current()->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            if (!Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/login?redirect='.static::generateCategoryUrl(Zira\Category::current()->name), true);
            } else {
                Zira\Response::forbidden();
            }
        }

        if (!$is_ajax) {
            $record = static::record(Zira\Category::current());

            // adding meta tags
            $title = Zira\Category::current()->title;
            if (Zira\Category::current()->meta_title) $meta_title = Zira\Category::current()->meta_title;
            else $meta_title = Zira\Category::current()->title;
            if (Zira\Category::current()->meta_keywords) $meta_keywords = Zira\Locale::t(Zira\Category::current()->meta_keywords);
            else $meta_keywords = mb_strtolower(Zira\Locale::t(Zira\Category::current()->title), CHARSET);
            if (Zira\Category::current()->meta_description) $meta_description = Zira\Category::current()->meta_description;
            else if (Zira\Category::current()->description) $meta_description = Zira\Category::current()->description;
            else $meta_description = Zira\Locale::t('Category: %s', Zira\Category::current()->title);
            $thumb = null;

            if ($record) {
                $title = $record->title;
                if ($record->meta_title) $meta_title = $record->meta_title;
                else $meta_title = $record->title;
                if ($record->meta_keywords) $meta_keywords = $record->meta_keywords;
                if ($record->meta_description) $meta_description = $record->meta_description;
                else $meta_description = $record->description;
                if ($record->thumb) $thumb = $record->thumb;
            }

            static::addTitle(Zira\Locale::t($meta_title));
            static::setKeywords($meta_keywords);
            static::setDescription(Zira\Locale::t($meta_description));
            static::addOpenGraphTags(Zira\Locale::t($meta_title), Zira\Locale::t($meta_description), static::generateCategoryUrl(Zira\Category::current()->name), $thumb);
        }

        $order = Zira\Page::getRecordsOrderColumn();
        
        $limit = Zira\Config::get('records_limit', 10);
        $limit_plus = 1;
        
        $use_pagination = Zira\Config::get('enable_pagination') && !$is_ajax;
        
        $pages = 1;
        if ($use_pagination || $order != 'id') {
            $records_count = static::getRecordsCount(Zira\Category::current(), false, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds());
            $pages = ceil($records_count / $limit);
        }
        
        if ($page === null) {
            $page = (int)Zira\Request::get('page');
        }
        if ($page > $pages) $page = $pages;
        if ($page < 1) $page = 1;

        if ($use_pagination || $order != 'id') $limit_plus = 0;
        if ($page > 1) $last_id = null;
        
        if (Zira\Category::current()->records_list===null || Zira\Category::current()->records_list) {
            $records = static::getRecords(Zira\Category::current(), false, $limit + $limit_plus, $last_id, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds(), $page, false, $order);
        } else {
            $records = array();
        }

        $rating_enabled = Zira\Category::current()->rating_enabled!==null ? Zira\Category::current()->rating_enabled : Zira\Config::get('rating_enabled', 0);
        $display_author = Zira\Category::current()->display_author!==null ? Zira\Category::current()->display_author : Zira\Config::get('display_author', 0);
        $display_date = Zira\Category::current()->display_date!==null ? Zira\Category::current()->display_date : Zira\Config::get('display_date', 0);

        $comments_enabled = Zira\Config::get('comments_enabled', 1);
        if (Zira\Category::current()->comments_enabled!==null) $comments_enabled = Zira\Category::current()->comments_enabled && $comments_enabled;
        
        $pagination = null;
        if ($use_pagination) {
            $pagination = new Zira\Pagination();
            $pagination->setLimit($limit);
            $pagination->setPage($page);
            $pagination->setTotal($records_count);
        }
        
        $data = array(
                static::VIEW_PLACEHOLDER_CLASS => 'records',
                static::VIEW_PLACEHOLDER_RECORDS => $records,
                static::VIEW_PLACEHOLDER_SETTINGS => array(
                    'limit' => $limit,
                    'comments_enabled' => $comments_enabled,
                    'rating_enabled' => $rating_enabled,
                    'display_author' => $display_author,
                    'display_date' => $display_date,
                    'page' => ($order != 'id') ? $page : null,
                    'pages' => ($order != 'id') ? $pages : null
                ),
                'pagination' => $pagination,
                'grid' => Zira\Config::get('site_records_grid', 1)
        );

        if (!$is_ajax) {
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $data, 'zira/list');
            $_data = array(
                static::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t($title)
            );

            $admin_icons = null;

            if ($record) {
                $_data[static::VIEW_PLACEHOLDER_IMAGE] = $record->image;
                $_data[static::VIEW_PLACEHOLDER_CONTENT] = $record->content;
                $_data[static::VIEW_PLACEHOLDER_CLASS] = 'parse-content';
                Zira\View::addParser();

                if (!static::allowPreview() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) && Zira\Permission::check(Zira\Permission::TO_EDIT_RECORDS)) {
                    $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-bookmark category', 'data-item'=>'/'.Zira\Category::current()->name));
                    $admin_icons .= '&nbsp;';
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-file record', 'data-item'=>$record->id));
                    $admin_icons .= Zira\Helper::tag_close('div');
                }
            } else {
                $_data[static::VIEW_PLACEHOLDER_DESCRIPTION] = Zira\Locale::t(Zira\Category::current()->description);

                if (!static::allowPreview() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
                    $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-bookmark category', 'data-item'=>'/'.Zira\Category::current()->name));
                    $admin_icons .= Zira\Helper::tag_close('div');
                }
            }

            $_data[static::VIEW_PLACEHOLDER_ADMIN_ICONS] = $admin_icons;

            static::render($_data);
        } else {
            $data[static::VIEW_PLACEHOLDER_CLASS] .= ' xhr-list';
            Zira\View::renderView($data, 'zira/list');
        }
    }

    public static function record($category) {
        $record = null;
        $record_name = $category->name;
        if (strpos($record_name,'/')!==false) {
            $record_name_parts = explode('/', $record_name);
            $record_name = array_pop($record_name_parts);
            $category_id = $category->parent_id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }

        if (!empty($record_name)) {
            $record = Zira\Models\Record::getCollection()
                        ->select(Zira\Models\Record::getFields())
                        ->where('category_id', '=', $category_id)
                        ->and_where('language', '=', Zira\Locale::getLanguage())
                        ->and_where('name', '=', $record_name)
                        ->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED)
                        ->get(0)
                        ;

            if ($record) {
                $slider_enabled = $category->slider_enabled!==null ? $category->slider_enabled : Zira\Config::get('slider_enabled', 1);
                $gallery_enabled = $category->gallery_enabled!==null ? $category->gallery_enabled : Zira\Config::get('gallery_enabled', 1);
                $files_enabled = $category->files_enabled!==null ? $category->files_enabled : Zira\Config::get('files_enabled', 1);
                $audio_enabled = $category->audio_enabled!==null ? $category->audio_enabled : Zira\Config::get('audio_enabled', 1);
                $video_enabled = $category->video_enabled!==null ? $category->video_enabled : Zira\Config::get('video_enabled', 1);

                if (!$record->slides_count) $slider_enabled = false;
                if (!$record->images_count) $gallery_enabled = false;
                if (!$record->files_count) $files_enabled = false;
                if (!$record->audio_count) $audio_enabled = false;
                if (!$record->video_count) $video_enabled = false;
                
                if (!$record->access_check || Zira\Permission::check(Zira\Permission::TO_VIEW_RECORD)) {
                    // not setting record id on category page
                    //static::setRecordId($record->id);
                    //static::setRecordUrl(static::generateRecordUrl(null, $record->name));
                    static::setCategoryPageRecordId($record->id);

                    // checking permission for gallery, files, audio & video
                    if (($record->gallery_check || $category->gallery_check || Zira\Config::get('gallery_check')) &&
                       !Zira\Permission::check(Zira\Permission::TO_VIEW_GALLERY)
                    ) {
                        $access_gallery = false;
                    } else {
                        $access_gallery = true;
                    }
                    if (($record->files_check || $category->files_check || Zira\Config::get('files_check')) && 
                        !Zira\Permission::check(Zira\Permission::TO_DOWNLOAD_FILES)
                    ) {
                        $access_files = false;
                    } else {
                        $access_files = true;
                    }
                    if (($record->audio_check || $category->audio_check || Zira\Config::get('audio_check')) && 
                       !Zira\Permission::check(Zira\Permission::TO_LISTEN_AUDIO)
                    ) {
                        $access_audio = false;
                    } else {
                        $access_audio = true;
                    }
                    if (($record->video_check || $category->video_check || Zira\Config::get('video_check')) && 
                       !Zira\Permission::check(Zira\Permission::TO_VIEW_VIDEO)
                    ) {
                        $access_video = false;
                    } else {
                        $access_video = true;
                    }
                        
                    if ($slider_enabled) {
                        $slides = static::getRecordSlides($record->id);
                        $slides_co = count($slides);
                    } else {
                        $slides = array();
                        $slides_co = 0;
                    }

                    if ($gallery_enabled && $access_gallery) {
                        $images = static::getRecordImages($record->id);
                        $images_co = count($images);
                    } else if ($gallery_enabled && !$access_gallery) {
                        $images = array();
                        $images_co = static::getRecordImagesCount($record->id);
                    } else {
                        $images = array();
                        $images_co = 0;
                    }
                    
                    if ($files_enabled && $access_files) {
                        $files = static::getRecordFiles($record->id);
                        $files_co = count($files);
                    } else if ($files_enabled && !$access_files) {
                        $files = array();
                        $files_co = static::getRecordFilesCount($record->id);
                    } else {
                        $files = array();
                        $files_co = 0;
                    }
                    
                    if ($audio_enabled && $access_audio) {
                        $audio = static::getRecordAudio($record->id);
                        $audio_co = count($audio);
                    } else if ($audio_enabled && !$access_audio) {
                        $audio = array();
                        $audio_co = static::getRecordAudioCount($record->id);
                    } else {
                        $audio = array();
                        $audio_co = 0;
                    }
                    
                    if ($video_enabled && $access_video) {
                        $video = static::getRecordVideos($record->id);
                        $video_co = count($video);
                    } else if ($video_enabled && !$access_video) {
                        $video = array();
                        $video_co = static::getRecordVideosCount($record->id);
                    } else {
                        $video = array();
                        $video_co = 0;
                    }

                    if ($slides_co > 0) static::setSlider($slides);
                    if ($images_co > 0) static::setGallery($images, $access_gallery);
                    if ($audio_co > 0) static::setAudio($audio, $access_audio);
                    if ($video_co > 0) static::setVideo($video, $access_video, $record->image);
                    if ($files_co > 0) static::setFiles($files, $access_files);

                    if ((!empty($slides) && $slider_enabled) || (!empty($video) && $video_enabled)) 
                        $record->image = null;
                } else {
                    $record = null;
                }
            }
        }
        return $record;
    }
}