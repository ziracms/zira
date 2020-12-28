<?php
/**
 * Zira project
 * theme.php
 * (c)2015 https://github.com/ziracms/zira
 * 
 * This file can be used to modify the contents before output
 * 
 * Requested page: Zira\Router::getRequest();
 * Current module: Zira\Router::getModule();
 * Current controller: Zira\Router::getController();
 * Current action: Zira\Router::getAction();
 * Request param: Zira\Router::getParam();
 * Content data: Zira\View::$data;
 * Content view: Zira\View::$view;
 * Content layout: Zira\View::$layout;
 * 
 * Example 1:
 * $layout_data = &Zira\View::getLayoutDataArray();
 * $layout_data['styles'] .= Zira\Helper::tag_short('link', array(
 *     'rel' => 'stylesheet',
 *     'type' => 'text/css',
 *     'href' => Zira\Helper::cssThemeUrl('extra.css')
 * ))."\r\n";
 * 
 * Example 2:
 * $widgets = &Zira\View::getDbWidgetsArray();
 * unset($widgets['sidebar_right']);
 * 
 * Custom thumbnail: 
 * Zira\Helper::baseUrl(Zira\Image::getCustomThumbUrl($record->image, $width, $height));
 */

if (!function_exists('renderPreloader')) {
    function renderPreloader() {
        render(array(), 'preloader');
    }
}

if (!function_exists('renderPageImage')) {
    function renderPageImage() {
        if (!empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_IMAGE])) {
            echo Zira\Helper::tag_short('img', array(
                'class' => 'image zira-lightbox',
                'src' => Zira\Helper::urlencode(Zira\Helper::baseUrl(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_IMAGE])),
                'alt' => !empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_TITLE]) ? Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_TITLE] : ''
            ));
        }
    }
}

if (!function_exists('renderPageTopComponents')) {
    function renderPageTopComponents() {
        $sliderType = Zira\Config::get('slider_type');
        if (!Zira\Router::getRequest() && Zira\Router::getModule()==DEFAULT_MODULE &&
            Zira\Router::getController()==DEFAULT_CONTROLLER &&
            Zira\Router::getAction()==DEFAULT_ACTION
        ) {
            $sliderType = Zira\Config::get('home_slider_type', $sliderType);
        }
        if (empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_IMAGE]) &&
            (empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_SLIDER_DATA]) || 
            $sliderType == 'fullscreen') &&
            empty(Zira\View::$data[Zira\Page::VIEW_PLACEHOLDER_VIDEO_DATA])
        ) return;
        if (Zira\Router::getRequest() && Zira\Router::getModule()==DEFAULT_MODULE && 
            Zira\Router::getController()=='contact'
        ) return;
        echo Zira\Helper::tag_open('div', array('class'=>'page-top-components'));
        echo Zira\Helper::tag_open('div', array('class'=>'container'));
        echo Zira\Helper::tag_open('div', array('class'=>'row'));
        echo Zira\Helper::tag_open('div', array('class'=>'col-sm-12'));
        renderPageImage();
        renderSlider();
        renderVideo();
        echo Zira\Helper::tag_close('div');
        echo Zira\Helper::tag_close('div');
        echo Zira\Helper::tag_close('div');
        echo Zira\Helper::tag_close('div');
    }
}

$layout_data = &Zira\View::getLayoutDataArray();

$layout_data['styles'] = Zira\Helper::tag_short('link', array(
                                'rel' => 'stylesheet',
                                'href' => 'https://fonts.googleapis.com/css2?family=Raleway&display=swap'
                            ))."\r\n".$layout_data['styles'];

Zira\View::addBodyBottomScript(Zira\Helper::tag('script', null, array(
                                'src' => Zira\Helper::jsThemeUrl('theme.js')
                            )));

// adding ie8.css
Zira\View::$head_addon .= '<!--[if lt IE 9]><link href="'.Zira\Helper::baseUrl(THEMES_DIR.'/'.Zira\View::getTheme().'/'.ASSETS_DIR.'/'.CSS_DIR.'/ie8.css').'" type="text/css" rel="stylesheet" /><![endif]-->';

return true;