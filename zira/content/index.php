<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Content;

use Zira;

class Index extends Zira\Page {
    public static function content() {
        $record = static::record();
        $categories = static::categories();

        if (!empty($categories)) {
            $layout = Zira\Config::get('layout');
            if (static::getLayout()!==null) $layout = static::getLayout();
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array(
                'grid' => $layout != Zira\View::LAYOUT_ALL_SIDEBARS,
                'categories' => $categories
            ), 'zira/home');
        }

        // adding meta tags
        $title = Zira\Config::get('home_title');
        $meta_title = Zira\Config::get('home_window_title');
        $meta_keywords = Zira\Config::get('home_keywords');
        $meta_description = Zira\Config::get('home_description');
        $thumb = null;
        if ($record) {
            if (!$title) $title = $record->title;
            if (!$meta_title) {
                if ($record->meta_title) $meta_title = $record->meta_title;
                else $meta_title = $record->title;
            }
            if (!$meta_description) {
                if ($record->meta_description) $meta_description = $record->meta_description;
                else $meta_description = $record->description;
            }
            if (!$meta_keywords) $meta_keywords = $record->meta_keywords;
            if ($record->thumb) $thumb = $record->thumb;
        } else {
            if (!$title) $title = Zira\Config::get('site_name');
            if (!$meta_title) $meta_title = Zira\Config::get('site_title');
            if (!$meta_keywords) $meta_keywords = Zira\Config::get('site_keywords');
            if (!$meta_description) $meta_description = Zira\Config::get('site_description');
        }

        static::setTitle(Zira\Locale::t($meta_title));
        static::setKeywords(Zira\Locale::t($meta_keywords));
        static::setDescription(Zira\Locale::t($meta_description));
        static::addOpenGraphTags(Zira\Locale::t($meta_title), Zira\Locale::t($meta_description), '', $thumb);

        //Zira\View::setRenderBreadcrumbs(false);

        $data = array(
            static::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t($title)
        );

        $admin_icons = null;

        if ($record) {
            $data[static::VIEW_PLACEHOLDER_IMAGE] = $record->image;
            $data[static::VIEW_PLACEHOLDER_CONTENT] = $record->content;
            $data[static::VIEW_PLACEHOLDER_CLASS] = 'parse-content';
            Zira\View::addParser();

            if (!static::allowPreview() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) && Zira\Permission::check(Zira\Permission::TO_EDIT_RECORDS)) {
                $admin_icons = Zira\Helper::tag_open('div', array('class'=>'editor-links-wrapper'));
                $admin_icons .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-file record', 'data-item'=>$record->id));
                $admin_icons .= Zira\Helper::tag_close('div');
            }

            $data[static::VIEW_PLACEHOLDER_ADMIN_ICONS] = $admin_icons;
        } else {
            $data[static::VIEW_PLACEHOLDER_DESCRIPTION] = Zira\Locale::t($meta_description);
        }

        parent::render($data);
    }

    public static function record() {
        $record = null;
        $record_name = Zira\Config::get('home_record_name');
        if (!empty($record_name)) {
            $record = Zira\Models\Record::getCollection()
                        ->select(Zira\Models\Record::getFields())
                        ->where('category_id', '=', Zira\Category::ROOT_CATEGORY_ID)
                        ->and_where('language', '=', Zira\Locale::getLanguage())
                        ->and_where('name', '=', $record_name)
                        ->get(0)
                        ;

            if ($record) {
                $slider_enabled = Zira\Config::get('slider_enabled', 1);
                $gallery_enabled = Zira\Config::get('gallery_enabled', 1);

                if (!$record->access_check || Zira\Permission::check(Zira\Permission::TO_VIEW_RECORD)) {
                    static::setRecordId($record->id);
                    static::setRecordUrl(static::generateRecordUrl(null, $record->name));

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

    public static function categories() {
        $limit = Zira\Config::get('home_records_limit');
        if (!$limit) $limit = Zira\Config::get('records_limit', 10);

        $categories = array();
        if (Zira\Config::get('home_records_enabled', true)) {
            $categories_cache_key = 'home.categories.'.Zira\Locale::getLanguage();
            $cached_categories = Zira\Cache::getArray($categories_cache_key);
            if ($cached_categories!==false) {
                $categories = $cached_categories;
            } else {
                // root category records
                $records = Zira\Models\Record::getCollection()
                    ->select('id', 'name', 'author_id', 'title', 'description', 'thumb', 'creation_date', 'rating', 'comments')
                    ->join(Zira\Models\User::getClass(), array('author_username' => 'username', 'author_firstname' => 'firstname', 'author_secondname' => 'secondname'))
                    ->where('category_id', '=', Zira\Category::ROOT_CATEGORY_ID)
                    ->and_where('language', '=', Zira\Locale::getLanguage())
                    ->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED)
                    ->and_where('front_page', '=', Zira\Models\Record::STATUS_FRONT_PAGE)
                    ->order_by('id', 'desc')
                    ->limit($limit)
                    ->get();

                if ($records) {
                    for ($i = 0; $i < count($records); $i++) {
                        $records[$i]->category_name = '';
                        $records[$i]->category_title = '';
                    }
                    $categories [] = array(
                        'title' => '',
                        'url' => '',
                        'records' => $records,
                        'settings' => array(
                            'comments_enabled' => Zira\Config::get('comments_enabled', 1),
                            'rating_enabled' => Zira\Config::get('rating_enabled', 0),
                            'display_author' => Zira\Config::get('display_author', 0),
                            'display_date' => Zira\Config::get('display_date', 0)
                        )
                    );
                }

                // top level category records
                $top_categories = Zira\Models\Category::getHomeCategories();

                $includeChilds = Zira\Config::get('category_childs_list', true);
                if ($includeChilds && CACHE_CATEGORIES_LIST) {
                    $all_categories = Zira\Category::getAllCategories();
                }
                foreach ($top_categories as $category) {
                    // categories are cached
                    //if ($category->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) continue;

                    $comments_enabled = $category->comments_enabled !== null ? $category->comments_enabled : Zira\Config::get('comments_enabled', 1);
                    $rating_enabled = $category->rating_enabled !== null ? $category->rating_enabled : Zira\Config::get('rating_enabled', 0);
                    $display_author = $category->display_author !== null ? $category->display_author : Zira\Config::get('display_author', 0);
                    $display_date = $category->display_date !== null ? $category->display_date : Zira\Config::get('display_date', 0);

                    $childs = null;
                    if ($includeChilds && CACHE_CATEGORIES_LIST && isset($all_categories)) {
                        $childs = array();
                        foreach($all_categories as $_category) {
                            // categories are cached
                            //if ($_category->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) continue;
                            if (mb_strpos($_category->name, $category->name . '/', null, CHARSET) === 0) {
                                $childs []= $_category;
                            }
                        }
                    }

                    $categories [] = array(
                        'title' => Zira\Locale::t($category->title),
                        'url' => static::generateCategoryUrl($category->name),
                        'records' => static::getRecords($category, true, $limit, null, $includeChilds, $childs),
                        'settings' => array(
                            'comments_enabled' => $comments_enabled,
                            'rating_enabled' => $rating_enabled,
                            'display_author' => $display_author,
                            'display_date' => $display_date
                        )
                    );
                }

                Zira\Cache::setArray($categories_cache_key, $categories);
            }
        }
        return $categories;
    }
}