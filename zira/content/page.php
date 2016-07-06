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
            $comments_enabled = Zira\Config::get('comments_enabled', 1);
            $rating_enabled = Zira\Config::get('rating_enabled', 0);
            $display_author = Zira\Config::get('display_author', 0);
            $display_date = Zira\Config::get('display_date', 0);
        } else {
            $slider_enabled = Zira\Category::current()->slider_enabled!==null ? Zira\Category::current()->slider_enabled : Zira\Config::get('slider_enabled', 1);
            $gallery_enabled = Zira\Category::current()->gallery_enabled!==null ? Zira\Category::current()->gallery_enabled : Zira\Config::get('gallery_enabled', 1);
            $comments_enabled = Zira\Category::current()->comments_enabled!==null ? Zira\Category::current()->comments_enabled : Zira\Config::get('comments_enabled', 1);
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
            Zira\Response::redirect('/');
        }

        static::$_record_id = $row->id;

        if (Zira\Category::current()) static::$_record_url = static::generateRecordUrl(Zira\Category::current()->name, $row->name);
        else static::$_record_url = static::generateRecordUrl(null, $row->name);

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
        if ($row->thumb) $thumb = $row->thumb;
        else $thumb = null;

        static::addTitle($meta_title);
        static::setKeywords($row->meta_keywords);
        static::setDescription($meta_description);
        static::addOpenGraphTags($meta_title, $meta_description, static::$_record_url, $thumb);

        if ($display_author) {
            $author = Zira\User::generateUserProfileLink($row->author_id, $row->author_firstname, $row->author_secondname, $row->author_username, 'author');
        } else {
            $author = null;
        }

        if ($slider_enabled) {
            $slides = Zira\Models\Slide::getCollection()
                            ->where('record_id','=',$row->id)
                            ->order_by('id', 'asc')
                            ->get();
        } else {
            $slides = null;
        }

        if ($gallery_enabled) {
            $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$row->id)
                            ->order_by('id', 'asc')
                            ->get();
        } else {
            $images = null;
        }

        if (!empty($slides) && $slider_enabled) {
            static::setSlider($slides);
        }
        if (!empty($images) && $gallery_enabled) {
            static::setGallery($images);
        }
        if ($comments_enabled) {
            static::setComments($row, $preview);
        }

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
            static::VIEW_PLACEHOLDER_IMAGE => empty($slides) || !$slider_enabled ? $row->image : null,
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