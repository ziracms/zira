<?php
/**
 * Zira project.
 * xml.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Controllers;

use Zira;

class Xml extends Zira\Controller {
    /**
     * Sitemap.xml
     */
    public function sitemap() {
        header('Content-type: application/xml; charset=utf-8');

        if (!Zira\Router::getLanguage() && count(Zira\Config::get('languages'))>1) {
            Zira\Helper::setAddingLanguageToUrl(false);
            $sitemaps = array();
            foreach(Zira\Config::get('languages') as $language) {
                $sitemaps []= Zira\Helper::url($language . '/sitemap.xml', true, true);
            }
            Zira\View::renderView(array('sitemaps'=>$sitemaps), 'zira/xml/sitemap-index');
        } else {
            $rows = Zira\Models\Record::getCollection()
                        ->select(Zira\Models\Record::getFields())
                        ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_access_check'=>'access_check'))
                        ->where('language','=',Zira\Locale::getLanguage())
                        ->and_where('published','=',Zira\Models\Record::STATUS_PUBLISHED)
                        ->order_by('id','desc')
                        ->limit(10000)
                        ->get();

            $urls = array();
            $home_page_name = Zira\Config::get('home_record_name');
            foreach($rows as $row) {
                if ($row->access_check || $row->category_access_check) continue;
                if ($home_page_name && $row->name == $home_page_name && $row->category_id == Zira\Category::ROOT_CATEGORY_ID) {
                    continue;
                }
                $urls []= Zira\Helper::url(Zira\Page::generateRecordUrl($row->category_name, $row->name), true, true);
            }

            Zira\View::renderView(array('urls'=>$urls), 'zira/xml/sitemap');
        }
    }

    /**
     * RSS feed
     */
    public function rss() {
        header('Content-type: application/rss+xml; charset=utf-8');

        if (Zira\Config::get('site_title')) {
            $title = Zira\Locale::t(Zira\Config::get('site_title'));
        } else if (Zira\Config::get('site_name')) {
            $title = Zira\Locale::t(Zira\Config::get('site_name'));
        } else {
            $title = Zira\Locale::t(DEFAULT_TITLE);
        }
        if (Zira\Config::get('site_description')) {
            $description = Zira\Locale::t(Zira\Config::get('site_description'));
        } else if (Zira\Config::get('site_slogan')) {
            $description = Zira\Locale::t(Zira\Config::get('site_slogan'));
        } else if (Zira\Config::get('site_name')) {
            $description = Zira\Locale::t(Zira\Config::get('site_name'));
        } else {
            $description = Zira\Locale::t(DEFAULT_TITLE);
        }

        $rows = Zira\Models\Record::getCollection()
                    ->select(Zira\Models\Record::getFields())
                    ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title', 'category_access_check'=>'access_check'))
                    ->where('language','=',Zira\Locale::getLanguage())
                    ->and_where('published','=',Zira\Models\Record::STATUS_PUBLISHED)
                    ->order_by('id','desc')
                    ->limit(20)
                    ->get();

        $items = array();
        $home_page_name = Zira\Config::get('home_record_name');
        foreach($rows as $row) {
            if ($row->access_check || $row->category_access_check) continue;
            if ($home_page_name && $row->name == $home_page_name && $row->category_id == Zira\Category::ROOT_CATEGORY_ID) {
                continue;
            }
            $image = array();
            if ($row->thumb && file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $row->thumb)) {
                $size = @getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $row->thumb);
                if ($size) {
                    $image = array(
                        'url' => Zira\Helper::baseUrl($row->thumb, true, true),
                        'length' => filesize(ROOT_DIR . DIRECTORY_SEPARATOR . $row->thumb),
                        'type' => $size['mime']
                    );
                }
            }
            $items []= array(
                'title' => $row->title,
                'url' => Zira\Helper::url(Zira\Page::generateRecordUrl($row->category_name, $row->name), true, true),
                'description' => $row->description,
                'image' => $image,
                'category' => $row->category_title,
                'date' => strtotime($row->creation_date)
            );
        }

        Zira\View::renderView(array(
            'title' => $title,
            'url' => Zira\Helper::url('', true, true),
            'description' => $description,
            'logo' => Zira\Config::get('site_logo') ? Zira\Helper::baseUrl(Zira\Config::get('site_logo'),true,true) : '',
            'channel_url' => Zira\Helper::url('rss', true, true),
            'items' => $items
        ), 'zira/xml/rss');
    }
}