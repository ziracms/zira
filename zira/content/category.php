<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Content;

use Zira;

class Category extends Zira\Page {
    public static function content($last_id = null, $is_ajax = false) {
        if (!Zira\Category::current()) return;

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

        $limit = Zira\Config::get('records_limit', 10);
        $limit_plus = 1;
        
        $use_pagination = Zira\Config::get('enable_pagination') && !$is_ajax;
        
        $pages = 1;
        if ($use_pagination) {
            $records_count = static::getRecordsCount(Zira\Category::current(), false, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds());
            $pages = ceil($records_count / $limit);
        }
        
        $page = (int)Zira\Request::get('page');
        if ($page > $pages) $page = $pages;
        if ($page < 1) $page = 1;
        
        if ($use_pagination) $limit_plus = 0;
        
        if (Zira\Category::current()->records_list===null || Zira\Category::current()->records_list) {
            $records = static::getRecords(Zira\Category::current(), false, $limit + $limit_plus, $last_id, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds(), $page);
        } else {
            $records = array();
        }

        $comments_enabled = Zira\Category::current()->comments_enabled!==null ? Zira\Category::current()->comments_enabled : Zira\Config::get('comments_enabled', 1);
        $rating_enabled = Zira\Category::current()->rating_enabled!==null ? Zira\Category::current()->rating_enabled : Zira\Config::get('rating_enabled', 0);
        $display_author = Zira\Category::current()->display_author!==null ? Zira\Category::current()->display_author : Zira\Config::get('display_author', 0);
        $display_date = Zira\Category::current()->display_date!==null ? Zira\Category::current()->display_date : Zira\Config::get('display_date', 0);

        $pagination = null;
        if ($use_pagination) {
            $pagination = new Zira\Pagination();
            $pagination->setLimit($limit);
            $pagination->setPage($page);
            $pagination->setPages($pages);
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
                    'display_date' => $display_date
                ),
                'pagination' => $pagination
        );

        if (!$is_ajax) {
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $data, 'zira/list');
            Zira\View::preloadThemeLoader();

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

    public static function placeholderContent($add_title = false, $add_description = false, $add_meta = false) {
        if (!Zira\Category::current()) return;

        // checking permission
        if (Zira\Category::current()->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            return;
        }

        $record = Zira\Content\Category::record(Zira\Category::current());

        if ($add_title) {
            $title = Zira\Category::current()->title;
        }

        // adding meta tags
        if ($add_meta) {
            if (Zira\Category::current()->meta_title) $meta_title = Zira\Category::current()->meta_title;
            else $meta_title = Zira\Category::current()->title;
            if (Zira\Category::current()->meta_keywords) $meta_keywords = Zira\Locale::t(Zira\Category::current()->meta_keywords);
            else $meta_keywords = mb_strtolower(Zira\Locale::t(Zira\Category::current()->title), CHARSET);
            if (Zira\Category::current()->meta_description) $meta_description = Zira\Category::current()->meta_description;
            else if (Zira\Category::current()->description) $meta_description = Zira\Category::current()->description;
            else $meta_description = Zira\Locale::t('Category: %s', Zira\Category::current()->title);
            $thumb = null;

            if ($record) {
                if ($add_title) {
                    $title = $record->title;
                }
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
        }

        $limit = Zira\Config::get('records_limit', 10);
        if (Zira\Category::current()->records_list===null || Zira\Category::current()->records_list) {
            $records = static::getRecords(Zira\Category::current(), false, $limit + 1, null, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds());
        } else {
            $records = array();
        }

        $comments_enabled = Zira\Category::current()->comments_enabled!==null ? Zira\Category::current()->comments_enabled : Zira\Config::get('comments_enabled', 1);
        $rating_enabled = Zira\Category::current()->rating_enabled!==null ? Zira\Category::current()->rating_enabled : Zira\Config::get('rating_enabled', 0);
        $display_author = Zira\Category::current()->display_author!==null ? Zira\Category::current()->display_author : Zira\Config::get('display_author', 0);
        $display_date = Zira\Category::current()->display_date!==null ? Zira\Category::current()->display_date : Zira\Config::get('display_date', 0);

        $data = array(
                static::VIEW_PLACEHOLDER_CLASS => 'records',
                static::VIEW_PLACEHOLDER_RECORDS => $records,
                static::VIEW_PLACEHOLDER_SETTINGS => array(
                    'limit' => $limit,
                    'comments_enabled' => $comments_enabled,
                    'rating_enabled' => $rating_enabled,
                    'display_author' => $display_author,
                    'display_date' => $display_date
                )
        );

        if ($add_title) {
            $_data = array(
                static::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t($title)
            );
        } else {
            $_data = array();
        }

        if ($record) {
            $_data[static::VIEW_PLACEHOLDER_IMAGE] = $record->image;
            $_data[static::VIEW_PLACEHOLDER_CONTENT] = $record->content;
            $_data[static::VIEW_PLACEHOLDER_CLASS] = 'parse-content';
            Zira\View::addParser();
        } else if ($add_description) {
            $_data[static::VIEW_PLACEHOLDER_DESCRIPTION] = Zira\Locale::t(Zira\Category::current()->description);
        }

        if ($add_title) {
            $admin_icons = null;
            if ($record) {
                if (!static::allowPreview() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) && Zira\Permission::check(Zira\Permission::TO_EDIT_RECORDS)) {
                    $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-bookmark category', 'data-item'=>'/'.Zira\Category::current()->name));
                    $admin_icons .= '&nbsp;';
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-file record', 'data-item'=>$record->id));
                    $admin_icons .= Zira\Helper::tag_close('div');
                }
            } else {
                if (!static::allowPreview() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
                    $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
                    $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-bookmark category', 'data-item'=>'/'.Zira\Category::current()->name));
                    $admin_icons .= Zira\Helper::tag_close('div');
                }
            }
            $_data[static::VIEW_PLACEHOLDER_ADMIN_ICONS] = $admin_icons;
        }

        if (!empty($_data)) {
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $_data, 'page');
        }

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $data, 'zira/list');
        Zira\View::preloadThemeLoader();
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
                        ->get(0)
                        ;

            if ($record) {
                $slider_enabled = $category->slider_enabled!==null ? $category->slider_enabled : Zira\Config::get('slider_enabled', 1);
                $gallery_enabled = $category->gallery_enabled!==null ? $category->gallery_enabled : Zira\Config::get('gallery_enabled', 1);

                if (!$record->access_check || Zira\Permission::check(Zira\Permission::TO_VIEW_RECORD)) {
//                    static::setRecordId($record->id);
//                    static::setRecordUrl(static::generateRecordUrl(null, $record->name));

                    if ($slider_enabled) {
                        $slides = Zira\Models\Slide::getCollection()
                            ->where('record_id', '=', $record->id)
                            ->order_by('id', 'asc')
                            ->get();
                    } else {
                        $slides = null;
                    }

                    if ($gallery_enabled) {
                        $images = Zira\Models\Image::getCollection()
                            ->where('record_id', '=', $record->id)
                            ->order_by('id', 'asc')
                            ->get();
                    } else {
                        $images = null;
                    }

                    if (!empty($slides) && $slider_enabled) static::setSlider($slides);
                    if (!empty($images) && $gallery_enabled) static::setGallery($images);

                    if (!empty($slides) && $slider_enabled) $record->image = null;
                } else {
                    $record = null;
                }
            }
        }
        return $record;
    }
}