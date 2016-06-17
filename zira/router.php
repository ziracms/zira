<?php
/**
 * Zira project
 * router.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class Router {
    protected static $request;
    protected static $language;
    protected static $module;
    protected static $controller;
    protected static $action;
    protected static $param;

    protected static $_map = array();

    public static function getRequest() {
        return self::$request;
    }

    public static function getLanguage() {
        return self::$language;
    }

    public static function getModule() {
        return self::$module;
    }

    public static function getController() {
        return self::$controller;
    }

    public static function getAction() {
        return self::$action;
    }

    public static function getParam() {
        return self::$param;
    }

    public static function setModule($value) {
        self::$module = $value;
    }

    public static function setController($value) {
        self::$controller = $value;
    }

    public static function setAction($value) {
        self::$action = $value;
    }

    public static function setParam($value) {
        self::$param = $value;
    }

    public static function addRoute($path,$module_controller_action) {
        self::$_map[$path] = $module_controller_action;
    }

    public static function isRouteExists($path) {
        return array_key_exists($path, self::$_map);
    }

    public static function isRouteAvailable($route) {
        if ($route == 'zira') return true;
        if (self::isRouteExists($route)) return false;
        $parts = explode('/',$route);
        if (count($parts)==1) {
            $parts[] = DEFAULT_CONTROLLER;
            $parts[] = DEFAULT_ACTION;
        } else if (count($parts)==2) {
            $parts[] = DEFAULT_ACTION;
        }
        $class = '\\'.ucfirst($parts[0]).'\\'.ucfirst(CONTROLLERS_DIR).'\\'.ucfirst($parts[1]);
        try {
            if (class_exists($class)) return false;
            if (method_exists($class, $parts[2])) return false;
            return true;
        } catch(\Exception $e) {
            return true;
        }
    }

    public static function dispatch() {
        self::$request = trim(urldecode(Request::uri()));
        self::$request = preg_replace('/^(.*?)\?(.*)$/iu','$1',self::$request);
        if (substr(self::$request,-1)!=='/') self::$request .= '/';

        $base_url = trim(BASE_URL,'/');
        if (!empty($base_url)) {
            self::$request = preg_replace('/^\/'.preg_quote($base_url).'(\/.*)$/iu','$1',self::$request);
        }
        $redirect = false;
        if (Config::get('clean_url') && preg_match('/^\/index\.php(\/.*)$/iu',self::$request,$matches)) {
            $redirect = true;
        }

        self::$request = preg_replace('/^\/index\.php(\/.*)$/iu','$1',self::$request);

        if (Config::get('languages') && count(Config::get('languages'))>1
            && preg_match('/^\/('.implode('|',Config::get('languages')).')(\/.*)$/iu',self::$request,$matches)
        ) {
            self::$language = $matches[1];
            self::$request = $matches[2];
        }

        self::$request = trim(self::$request,'/');

        if ($redirect) {
            if (self::$language) self::$request = self::$language.'/'.self::$request;
            Response::redirect(self::$request, true);
        }

        self::$param = '';
        $url_parts = array();
        if (preg_match('/^([a-z\/]+)\/(.*)/',self::$request.'/',$matches)) {
            $url_parts = explode('/',$matches[1]);
            $rewrite = '';
            $rewrite_match = '';
            if (array_key_exists($matches[1],self::$_map)) {
                $rewrite_match = $matches[1];
                $rewrite = self::$_map[$matches[1]];
            } else {
                $variants = array();
                if (count($url_parts) == 2) {
                    $variants[]='*/'.$url_parts[1];
                    $variants[]=$url_parts[0].'/*';
                } else if (count($url_parts) == 3) {
                    $variants[]='*/'.$url_parts[1];
                    $variants[]=$url_parts[0].'/*';
                    $variants[]='*/*/'.$url_parts[2];
                    $variants[]='*/'.$url_parts[1].'/*';
                    $variants[]=$url_parts[0].'/*/*';
                    $variants[]='*/'.$url_parts[1].'/'.$url_parts[2];
                    $variants[]=$url_parts[0].'/*/'.$url_parts[2];
                    $variants[]=$url_parts[0].'/'.$url_parts[1].'/*';
                }

                foreach($variants as $variant) {
                    if (array_key_exists($variant, self::$_map)) {
                        $rewrite = self::$_map[$variant];
                        $rewrite_match = $variant;
                        break;
                    }
                }
            }
            if (!empty($rewrite)) {
                for ($i=0; $i<3; $i++) {
                    if (strpos($rewrite, '$'.($i+1))!==false) {
                        if (isset($url_parts[$i])) {
                            $rewrite = str_replace('$'.($i+1), $url_parts[$i], $rewrite);
                        } else {
                            if ($i==0) $part = DEFAULT_MODULE;
                            else if ($i==1) $part = DEFAULT_CONTROLLER;
                            else $part = DEFAULT_ACTION;
                            $rewrite = str_replace('$'.($i+1), $part, $rewrite);
                        }
                    }
                }
                $_url_parts = explode('/', $rewrite);
                if (!empty($rewrite_match)) {
                    $_match_parts = explode('/', $rewrite_match);
                    if (count($_match_parts) < count($url_parts)) {
                        $_add_params = array_slice($url_parts, count($_match_parts));
                        $_url_parts = array_merge($_url_parts, $_add_params);
                    }
                }
                $url_parts = $_url_parts;
            }
            self::$param = trim($matches[2],'/');
        } else {
            self::$param = self::$request;
        }

        if (count($url_parts)==0) {
            self::$module = DEFAULT_MODULE;
            self::$controller = DEFAULT_CONTROLLER;
            self::$action = DEFAULT_ACTION;
        } else if (count($url_parts)==1) {
            self::$module = $url_parts[0];
            self::$controller = DEFAULT_CONTROLLER;
            self::$action = DEFAULT_ACTION;
        } else if (count($url_parts)==2) {
            self::$module = $url_parts[0];
            self::$controller = $url_parts[1];
            self::$action = DEFAULT_ACTION;
        } else {
            self::$module = array_shift($url_parts);
            self::$controller = array_shift($url_parts);
            self::$action = array_shift($url_parts);

            if (count($url_parts)>0) {
                $_param = implode('/',$url_parts);
                if (!empty(self::$param)) $_param .= '/';
                self::$param = $_param . self::$param;
            }
        }

        if (!self::$language && count(Config::get('languages'))>1 &&
            !empty(self::$request) &&
            self::$request!='cron' &&
            self::$request!='sitemap.xml' &&
            self::$module!='dash' &&
            self::$controller!='dash'
        ) {
            self::$language = Config::get('language');
            Response::redirect(self::$language.'/'.self::$request, true);
        } else if (self::$language && self::$language==Config::get('language') && empty(self::$request)) {
            Response::redirect(self::$request, true);
        }
    }
}