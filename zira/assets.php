<?php
/**
 * Zira project.
 * assets.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira;

class Assets {
    const CACHE_LIFETIME = 86400;
    const CSS_ASSETS_CACHE_FILE = '.css.cache';
    const JS_ASSETS_CACHE_FILE = '.js.cache';
    const CSS_CONTENT_ASSETS_CACHE_FILE = '.css.content.cache';
    const JS_CONTENT_ASSETS_CACHE_FILE = '.js.content.cache';
    const CSS_SCRIPT = 'assets/css/index.php';
    const JS_SCRIPT = 'assets/js/index.php';
    const CSS_SCRIPT_CLEAN = 'assets/css/cache';
    const JS_SCRIPT_CLEAN = 'assets/js/cache';

    protected static $_active = false;
    protected static $_gzip = null;
    protected static $_css_mtime = null;
    protected static $_js_mtime = null;

    protected static $_css_assets = array(
        'bootstrap.min.css',
        'bootstrap-datetimepicker.min.css',
        'bootstrap-theme.min.css',
        'cropper.css',
        'lightbox.css',
        'bxslider.css',
        'jplayer.css',
        'zira.css'
    );
    
    protected static $_theme_css = 'main.css';

    protected static $_js_assets = array(
        'jquery.min.js',
        'bootstrap.min.js',
        'moment.min.js',
        'bootstrap-datetimepicker.min.js',
        'cropper.js',
        'bxslider.min.js',
        'md5.js',
        'parse.js',
        'upload.inc.js',
        'upload.js',
        'zira.js'
    );
    
    protected static $_js_assets_if_in_body = array(
        'lightbox.min.js'
    );
    
    protected static $_css_assets_contents = array();
    protected static $_js_assets_contents = array();

    public static function getCSSAssets() {
        return self::$_css_assets;
    }

    public static function getJSAssets() {
        return self::$_js_assets;
    }

    public static function registerCSSAsset($asset) {
        self::$_css_assets [] = $asset;
    }

    public static function registerJSAsset($asset) {
        self::$_js_assets []= $asset;
    }
    
    public static function registerCSSAssetContent($content) {
        self::$_css_assets_contents [] = $content;
    }

    public static function registerJSAssetContent($content) {
        self::$_js_assets_contents []= $content;
    }

    public static function mergeCSS() {
        self::$_css_mtime = null;
        if (!is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR)) throw new \Exception('Cache dir is not writable');
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::CSS_ASSETS_CACHE_FILE;
        $css_file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;

        $f=fopen($css_file,'wb');
        if (!$f) throw new \Exception('Failed to open file');
        foreach(self::$_css_assets as $css_asset) {
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . CSS_DIR . DIRECTORY_SEPARATOR . $css_asset;
            if (!file_exists($path) || !is_readable($path)) throw new \Exception('Asset '.$css_asset.' not found');
            $data = '/** '.$css_asset.' **/'."\r\n\r\n";
            $content = file_get_contents($path);
            while(strpos($content, '../../')!==false) {
                $content = str_replace('../../', '../', $content);
            }
            $data .= $content;
            $data .= "\r\n\r\n";
            fwrite($f, $data);
        }
        
        $theme_css = self::getThemeCSS();
        fwrite($f, $theme_css);
        
        fclose($f);
        
        View::registerRenderHook(get_called_class(), 'mergeCSSContent');
    }
    
    public static function mergeCSSContent() {
        if (count(self::$_css_assets_contents)>0) {
            $content_url = CACHE_DIR . DIRECTORY_SEPARATOR . self::CSS_CONTENT_ASSETS_CACHE_FILE;
            $css_content_file = ROOT_DIR . DIRECTORY_SEPARATOR . $content_url;

            $cf=fopen($css_content_file,'wb');
            if ($cf) {
                foreach(self::$_css_assets_contents as $content) {
                    $data = '/** extra css **/'."\r\n\r\n";
                    $data .= $content;
                    $data .= "\r\n\r\n";
                    fwrite($cf, $data);
                }
                fclose($cf);
            }
        }
    }
    
    public static function getThemeCSS() {
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . CSS_DIR . DIRECTORY_SEPARATOR . self::$_theme_css;
        if (!file_exists($path) || !is_readable($path)) return;
        $data = '/** '.self::$_theme_css.' **/'."\r\n\r\n";
        $content = file_get_contents($path);
        while(strpos($content, '../')!==false) {
            $content = str_replace('../', rtrim(BASE_URL,'/') . '/' .THEMES_DIR.'/'.View::getTheme().'/'.ASSETS_DIR.'/', $content);
        }
        $data .= $content;
        $data .= "\r\n\r\n";
        
        return $data;
    }

    public static function mergeJS() {
        self::$_js_mtime = null;
        if (!is_writable(ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR)) throw new \Exception('Cache dir is not writable');
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::JS_ASSETS_CACHE_FILE;
        $js_file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;

        $f=fopen($js_file,'wb');
        if (!$f) throw new \Exception('Failed to open file');
        foreach(self::$_js_assets as $js_asset) {
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . $js_asset;
            if (!file_exists($path) || !is_readable($path)) throw new \Exception('Asset '.$js_asset.' not found');
            $data = '/** '.$js_asset.' **/'."\r\n\r\n";
            $data .= file_get_contents($path);
            $data .= "\r\n\r\n";
            fwrite($f, $data);
        }
        
        fclose($f);
        
        View::registerRenderHook(get_called_class(), 'mergeJSContent');
    }
    
    public static function mergeJSContent() {
        if (count(self::$_js_assets_contents)>0) {
            $content_url = CACHE_DIR . DIRECTORY_SEPARATOR . self::JS_CONTENT_ASSETS_CACHE_FILE;
            $js_content_file = ROOT_DIR . DIRECTORY_SEPARATOR . $content_url;

            $cf=fopen($js_content_file,'wb');
            if ($cf) {
                foreach(self::$_js_assets_contents as $content) {
                    $data = '/** extra js **/'."\r\n\r\n";
                    $data .= $content;
                    $data .= "\r\n\r\n";
                    fwrite($cf, $data);
                }
                fclose($cf);
            }
        }
    }

    public static function merge() {
        try {
            self::mergeCSS();
            self::mergeJS();
            return self::isCached();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getCSSMTime() {
        if (self::$_css_mtime!==null) return self::$_css_mtime;
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::CSS_ASSETS_CACHE_FILE;
        $file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;
        $mtime = filemtime($file);
        self::$_css_mtime = $mtime;
        return $mtime;
    }

    public static function getJSMTime() {
        if (self::$_js_mtime!==null) return self::$_js_mtime;
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::JS_ASSETS_CACHE_FILE;
        $file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;
        $mtime = filemtime($file);
        self::$_js_mtime = $mtime;
        return $mtime;
    }

    public static function isCSSExpired() {
        return time()-self::getCSSMTime()>self::CACHE_LIFETIME;
    }

    public static function isJSExpired() {
        return time()-self::getJSMTime()>self::CACHE_LIFETIME;
    }

    public static function isCSSCached() {
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::CSS_ASSETS_CACHE_FILE;
        $css_file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;
        return file_exists($css_file) && is_readable($css_file) && filesize($css_file)>0;
    }

    public static function isJSCached() {
        $url = CACHE_DIR . DIRECTORY_SEPARATOR . self::JS_ASSETS_CACHE_FILE;
        $js_file = ROOT_DIR . DIRECTORY_SEPARATOR . $url;
        return file_exists($js_file) && is_readable($js_file) && filesize($js_file)>0;
    }

    public static function isCached() {
        return self::isCSSCached() && self::isJSCached();
    }

    public static function isCachedAndNotExpired() {
        if (defined('DEBUG') && DEBUG) return false;
        return self::isCached() && !self::isCSSExpired() && !self::isJSExpired();
    }

    public static function setActive($active) {
        self::$_active = (bool)$active;
    }

    public static function isActive() {
        return self::$_active;
    }

    public static function isMergedCSS($file) {
        return in_array($file, self::$_css_assets);
    }

    public static function isMergedJS($file) {
        return in_array($file, self::$_js_assets);
    }

    public static function getCSSURL() {
        if (Config::get('clean_url')) {
            $url = Helper::baseUrl(self::CSS_SCRIPT_CLEAN);
        } else {
            $url = Helper::baseUrl(self::CSS_SCRIPT);
        }
        $q = '?';
        $q .= 't='.(intval(self::isGzipEnabled())+1).self::getCSSMTime();
        return $url.$q;
    }

    public static function getJSURL() {
        if (Config::get('clean_url')) {
            $url = Helper::baseUrl(self::JS_SCRIPT_CLEAN);
        } else {
            $url = Helper::baseUrl(self::JS_SCRIPT);
        }
        $q = '?';
        $q .= 't='.(intval(self::isGzipEnabled())+1).self::getJSMTime();
        return $url.$q;
    }

    public static function addStyle() {
        $attributes = array();
        $attributes['rel'] = 'stylesheet';
        $attributes['type'] = 'text/css';
        $attributes['href'] = self::getCSSURL();
        View::addHTML(Helper::tag_short('link', $attributes),View::VAR_STYLES);
    }

    public static function addScript() {
        $attributes = array();
        $attributes['src'] = self::getJSURL();
        View::addHTML(Helper::tag('script', null, $attributes),View::VAR_SCRIPTS);
    }

    public static function init() {
        if (Request::isAjax()) return;
        if (INSERT_SCRIPTS_TO_BODY) {
            self::$_js_assets = array_merge(self::$_js_assets, self::$_js_assets_if_in_body);
        }
        if (Config::get('caching') && self::isCachedAndNotExpired()) {
            self::setActive(true);
        } else if (Config::get('caching') && self::merge()) {
            self::setActive(true);
        } else {
            self::setActive(false);
        }
        if (self::isActive()) {
            self::addStyle();
            self::addScript();
        }
    }

    public static function isGzipEnabled() {
        if (self::$_gzip!==null) return self::$_gzip;
        $accept_encoding = '';
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match( '/\b(x-gzip|gzip)\b/', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), $match)) {
            $accept_encoding = $match[1];
        }
        if (empty($accept_encoding) && defined('FORCE_GZIP_ASSETS') && FORCE_GZIP_ASSETS) $accept_encoding = 'gzip';
        if (Config::get('gzip') && function_exists('gzencode') && !@ini_get('zlib.output_compression') && !empty($accept_encoding)) {
            self::$_gzip = true;
        } else {
            self::$_gzip = false;
        }
        return self::$_gzip;
    }
}