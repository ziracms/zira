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

// adding ie8.css
Zira\View::$head_addon .= '<!--[if lt IE 9]><link href="'.Zira\Helper::baseUrl(THEMES_DIR.'/'.Zira\View::getTheme().'/'.ASSETS_DIR.'/'.CSS_DIR.'/ie8.css').'" type="text/css" rel="stylesheet" /><![endif]-->';

return true;