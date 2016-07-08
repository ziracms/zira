<?php
/**
 * Zira project
 * response.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

use Dash\Dash;

class Response {
    public static $status = 200;

    const STATUS_404 = 404;
    const STATUS_403 = 403;
    const STATUS_500 = 500;

    const SESSION_REDIRECT = 'redirect_request';

    public static function setStatus($status) {
        self::$status = $status;
    }

    public static function getStatus() {
        return self::$status;
    }

    public static function redirect($path, $keep_params = false) {
        Session::set(self::SESSION_REDIRECT, true);

        $url = Helper::url($path);
        if ($keep_params) {
            $params = '';
            foreach(Request::get() as $key=>$value) {
                if ($key == Dash::GET_FRAME_PARAM && !Dash::isFrame()) continue;
                if (!empty($params)) $params .= '&';
                $params .= $key .'='. $value;
            }
            if (!empty($params)) {
                if (strpos($url, '?') === false) $url .= '?';
                else $url .= '&';
                $url .= $params;
            }
        }
        header('Location: '.$url);
        exit;
    }

    public static function notFound() {
        self::$status = self::STATUS_404;
        http_response_code(self::$status);
        if (View::isInitialized()) {
            View::addDefaultAssets();
            View::addThemeAssets();
            if (Config::get('site_window_title') && Config::get('site_title')) {
                $suffix = PAGE_TITLE_DELIMITER . Locale::t(Config::get('site_title'));
            } else {
                $suffix = '';
            }
            View::setLayoutData(array(View::VAR_TITLE=>Locale::t('Page not found').$suffix));
            View::render(array('code'=>self::$status,'message'=>Locale::t('Page not found')), 'error', View::LAYOUT_NO_SIDEBARS);
        } else {
            echo Locale::t('Page not found');
        }
        exit;
    }

    public static function forbidden() {
        self::$status = self::STATUS_403;
        http_response_code(self::$status);
        if (View::isInitialized()) {
            View::addDefaultAssets();
            View::addThemeAssets();
            if (Config::get('site_window_title') && Config::get('site_title')) {
                $suffix = PAGE_TITLE_DELIMITER . Locale::t(Config::get('site_title'));
            } else {
                $suffix = '';
            }
            View::setLayoutData(array(View::VAR_TITLE=>Locale::t('Access denied').$suffix));
            View::render(array('code'=>self::$status,'message'=>Locale::t('Access denied')), 'error', View::LAYOUT_NO_SIDEBARS);
        } else {
            echo Locale::t('Access denied');
        }
        exit;
    }

    public static function error($message) {
        self::$status = self::STATUS_500;
        http_response_code(self::$status);
        if (View::isInitialized()) {
            if (defined('DEBUG') && DEBUG && !View::isRenderStarted()) {
                View::addDefaultAssets();
                View::addThemeAssets();
                if (Config::get('site_window_title') && Config::get('site_title')) {
                    $suffix = PAGE_TITLE_DELIMITER . Locale::t(Config::get('site_title'));
                } else {
                    $suffix = '';
                }
                View::setLayoutData(array(View::VAR_TITLE=>Locale::t('An error occurred').$suffix));
            }
            View::render(array('code'=>self::$status,'message'=>Locale::t('An error occurred'),'content'=>$message), 'error', View::LAYOUT_NO_SIDEBARS);
        } else {
            echo $message;
        }
        exit;
    }

    public static function exception(\Exception $e) {
        if (defined('DEBUG') && DEBUG) {
            $str = Helper::tag('div', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
            $str .= Helper::tag_open('div');
            $str .= Helper::tag_open('code');
            $str .= nl2br($e->getTraceAsString());
            $str .= Helper::tag_close('code');
            $str .= Helper::tag_close('div');
        } else {
            $str = Helper::tag('div', $e->getMessage());
        }
        self:: error($str);
    }
}