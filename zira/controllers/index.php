<?php
/**
 * Zira project
 * index.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Index extends Zira\Controller {
    public function _before() {
        parent::_before();
    }

    /**
     * Home page
     */
    public function index() {
        $layout = Zira\Config::get('home_layout');
        if ($layout!==null) {
            Zira\Page::setLayout($layout);
        }
        Zira\Content\Index::content();
    }

    /**
     * Renders record page or category
     *
     * @param $param
     */
    public function page($param) {
        if (!empty($param)) {
            if (Zira\Category::current() && Zira\Category::current()->layout) {
                Zira\Page::setLayout(Zira\Category::current()->layout);
            }
            Zira\Content\Page::content($param, Zira\Page::allowPreview());
        } else if (Zira\Category::current()) {
            if (Zira\Category::current()->layout) {
                Zira\Page::setLayout(Zira\Category::current()->layout);
            }
            Zira\Content\Category::content();
        } else {
            Zira\Response::notFound();
        }
    }

    /**
     * 403 page
     */
    public function forbidden() {
        Zira\Response::forbidden();
    }

    /**
     * 404 page
     */
    public function notfound() {
        Zira\Response::notFound();
    }

    /**
     * Displays CAPTCHA image
     */
    public function captcha() {
        header('Content-Type: image/jpeg');
        Zira\Form\Form::generateCaptcha();
    }

    /**
     * Site map page
     */
    public function map() {
        $categories = Zira\Category::getCategoriesMap();

        Zira\Page::addTitle(Zira\Locale::t('Site map'));
        Zira\Page::addBreadcrumb('sitemap', Zira\Locale::t('Site map'));

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, array('categories'=>$categories), 'zira/map');
        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t('Site map'),
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => ''
        ));
    }
}
