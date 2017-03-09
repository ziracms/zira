<?php
/**
 * Zira project
 * theme.php
 * (c)2015 http://dro1d.ru
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
 * $placeholder_views = &Zira\View::getPlaceholderViewsArray();
 * $placeholder_views['content']['zira/comments'] = array();
 * 
 * Example 3:
 * $widgets = &Zira\View::getDbWidgetsArray();
 * unset($widgets['sidebar_right']);
 */

include(Zira\View::$layout);