<?php
/**
 * Zira project
 * view.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class View {
    // Layouts
    const LAYOUT_ALL_SIDEBARS = 'layout';
    const LAYOUT_LEFT_SIDEBAR = 'layout-left';
    const LAYOUT_RIGHT_SIDEBAR = 'layout-right';
    const LAYOUT_NO_SIDEBARS = 'layout-wide';

    const CUSTOM_LAYOUTS_FOLDER = 'layouts';

    // Layout vars
    const VAR_CHARSET = 'charset';
    const VAR_TITLE = 'title';
    const VAR_META = 'meta';
    const VAR_STYLES = 'styles';
    const VAR_SCRIPTS = 'scripts';
    const VAR_HEAD_TOP = 'head_top';
    const VAR_HEAD_BOTTOM = 'head_bottom';
    const VAR_BODY_TOP = 'body_top';
    const VAR_BODY_BOTTOM = 'body_bottom';
    const VAR_CONTENT_TOP = 'content_top';
    const VAR_CONTENT = 'content';
    const VAR_CONTENT_BOTTOM = 'content_bottom';
    const VAR_SIDEBAR_LEFT = 'sidebar_left';
    const VAR_SIDEBAR_RIGHT = 'sidebar_right';
    const VAR_HEADER = 'header';
    const VAR_FOOTER = 'footer';
    
    public static $data = array();
    public static $view = null;
    public static $layout = null;
    public static $body_class = '';

    protected static $_layout_data = array();
    protected static $_placeholder_views = array();

    protected static $_theme;
    protected static $_render_layout = true;
    protected static $is_ajax;

    protected static $_js_strings = array();

    protected static $_bootstrap_added = false;
    protected static $_jquery_added = false;
    protected static $_lightbox_added = false;
    protected static $_slider_added = false;
    protected static $_core_assets_added = false;
    protected static $_theme_assets_added = false;
    protected static $_cropper_assets_added = false;
    protected static $_tinymce_assets_added = false;
    protected static $_datepicker_assets_added = false;
    protected static $_datepicker_added = false;
    protected static $_loader_preloaded = false;
    protected static $_autocomplete_added = false;
    protected static $_parser_added = false;
    protected static $_codemirror_assets_added = false;
    protected static $_codemirror_added = false;
    protected static $_jplayer_assets_added = false;
    protected static $_colorpicker_assets_added = false;
    protected static $_colorpicker_added = false;

    protected static $_render_js_strings = true;
    protected static $_render_breadcrumbs = true;

    protected static $_keywords_added = false;
    protected static $_description_added = false;

    protected static $_render_started = false;
    protected static $_render_widgets = true;
    protected static $_render_db_widgets = true;

    protected static $_widgets = array();
    protected static $_db_widgets = array();
    protected static $_widget_objects = null;
    protected static $_db_widget_objects = null;

    protected static $_body_bottom_scripts = array();
    protected static $_before_render_callbacks = array();

    public static function isInitialized() {
        return self::$_theme !== null;
    }

    public static function isAjax() {
        if (self::$is_ajax==null) {
            return Request::isAjax();
        }
        return self::$is_ajax;
    }

    public static function setAjax($ajax) {
        self::$is_ajax = (bool) $ajax;
    }

    public static function setRenderJsStrings($render_js_strings) {
        self::$_render_js_strings = (bool) $render_js_strings;
    }

    public static function setRenderBreadcrumbs($render_breadcrumbs) {
        self::$_render_breadcrumbs = (bool) $render_breadcrumbs;
    }

    public static function renderBreadcrumbsEnabled() {
        return self::$_render_breadcrumbs;
    }

    public static function addJsStrings(array $strings) {
        self::$_js_strings = array_merge(self::$_js_strings, $strings);
    }
    
    public static function getJsStrings() {
        return self::$_js_strings;
    }
    
    public static function clearJsStrings() {
        self::$_js_strings = array();
    }

    public static function addMeta(array $attributes) {
        $html = '<meta ';
        foreach($attributes as $k=>$v) {
            $html .= Helper::html($k) . '="' . Helper::html($v) . '" ';
        }
        $html .= '/>';

        self::addHTML($html,self::VAR_META);
    }

    public static function addStyle($url, $theme = false, array $attributes = null) {
        if (Assets::isActive() && Assets::isMergedCSS($url)) return;
        if (!$attributes) $attributes = array();
        if (!isset($attributes['rel'])) $attributes['rel'] = 'stylesheet';
        if (!isset($attributes['type'])) $attributes['type'] = 'text/css';
        if (!$theme) {
            $attributes['href'] = Helper::cssUrl($url);
        } else {
            $attributes['href'] = Helper::cssThemeUrl($url);
        }
        self::addHTML(Helper::tag_short('link', $attributes),self::VAR_STYLES);
    }

    public static function addThemeStyle($url, array $attributes = null) {
        self::addStyle($url, true, $attributes);
    }

    public static function addScript($url, $theme = false, array $attributes = null) {
        if (Assets::isActive() && Assets::isMergedJS($url)) return;
        if (!$attributes) $attributes = array();
        if (!$theme) {
            $attributes['src'] = Helper::jsUrl($url);
        } else {
            $attributes['src'] = Helper::jsThemeUrl($url);
        }
        self::addHTML(Helper::tag('script', null, $attributes),self::VAR_SCRIPTS);
    }

    public static function addThemeScript($url, array $attributes = null) {
        self::addScript($url, true, $attributes);
    }

    public static function addHTML($html,$var) {
        if (!isset(self::$_layout_data[$var])) self::$_layout_data[$var] = $html . "\r\n";
        else self::$_layout_data[$var] .= $html . "\r\n";
    }

    public static function isRenderStarted() {
        return self::$_render_started;
    }

    public static function setKeywordsAdded($value) {
        self::$_keywords_added = (bool)$value;
    }

    public static function setDescriptionAdded($value) {
        self::$_description_added = (bool)$value;
    }
    
    public static function registerRenderHook($object, $method) {
        self::$_before_render_callbacks []= array($object, $method);
    }

    public static function render(array $data, $view=null, $layout=null) {
        require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'tpl.php');
        
        foreach(self::$_before_render_callbacks as $callback) {
            try {
                call_user_func($callback);
            } catch (Exception $e) {
                // ignore
            }
        }

        if (!$view) {
            $view = Router::getModule() . DIRECTORY_SEPARATOR .
                Router::getController() . DIRECTORY_SEPARATOR .
                Router::getAction();
        } else {
            $view = str_replace('/', DIRECTORY_SEPARATOR, $view);
        }

        $view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            THEMES_DIR . DIRECTORY_SEPARATOR .
            self::$_theme . DIRECTORY_SEPARATOR .
            $view . '.php';

        if (self::$_theme!=DEFAULT_THEME && !file_exists($view_file)) {
            $view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                        THEMES_DIR . DIRECTORY_SEPARATOR .
                        DEFAULT_THEME . DIRECTORY_SEPARATOR .
                        $view . '.php';
        }

        if (self::$_render_layout && !self::$_render_started) {
            if (!$layout) {
                $layout = Config::get('layout');
            }

            $layout = str_replace('/', DIRECTORY_SEPARATOR, $layout);

            $layout_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                THEMES_DIR . DIRECTORY_SEPARATOR .
                self::$_theme . DIRECTORY_SEPARATOR .
                $layout . '.php';

            if (self::$_theme!=DEFAULT_THEME && !file_exists($layout_file)) {
                $layout_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                            THEMES_DIR . DIRECTORY_SEPARATOR .
                            DEFAULT_THEME . DIRECTORY_SEPARATOR .
                            $layout . '.php';
            }
            
            $default_layouts = self::getDefaultLayouts();
            if (!file_exists($layout_file) && !array_key_exists($layout, $default_layouts)) {
                $layout_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                            THEMES_DIR . DIRECTORY_SEPARATOR .
                            self::$_theme . DIRECTORY_SEPARATOR .
                            self::LAYOUT_ALL_SIDEBARS . '.php';
            }

            self::$data = $data;
            self::$view = $view_file;
            self::$layout = $layout_file;
            
            self::renderLayout();

            if (defined('DEBUG') && DEBUG && defined('START_TIME')) {
                echo "\r\n".'<!--Memory usage: '.(memory_get_usage(true)/1024).' kB-->';
                echo "\r\n".'<!--Peak memory usage: '.(memory_get_peak_usage(true)/1024).' kB-->';
                if (defined('START_TIME')) {
                    echo "\r\n".'<!--Execution time: '.number_format((microtime(true)-START_TIME)*1000,2).' ms-->';
                }
                echo "\r\n".'<!--DB queries: '.Db\Db::getTotal().'-->';
            }
        } else {
            self::renderContent($data, $view_file);
        }
    }

    public static function renderView(array $data, $view) {
        require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'tpl.php');

        $view = str_replace('/', DIRECTORY_SEPARATOR, $view);

        $view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            THEMES_DIR . DIRECTORY_SEPARATOR .
            self::$_theme . DIRECTORY_SEPARATOR .
            $view . '.php';

        if (self::$_theme!=DEFAULT_THEME && !file_exists($view_file)) {
            $view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
                        THEMES_DIR . DIRECTORY_SEPARATOR .
                        DEFAULT_THEME . DIRECTORY_SEPARATOR .
                        $view . '.php';
        }

        self::renderContent($data, $view_file);
    }

    public static function isViewExists($view=null) {
        if (!$view) {
            $view = Router::getModule() . DIRECTORY_SEPARATOR .
                Router::getController() . DIRECTORY_SEPARATOR .
                Router::getAction();
        } else {
            $view = str_replace('/', DIRECTORY_SEPARATOR, $view);
        }

        $view_file = ROOT_DIR . DIRECTORY_SEPARATOR .
            THEMES_DIR . DIRECTORY_SEPARATOR .
            self::$_theme . DIRECTORY_SEPARATOR .
            $view . '.php';

        return file_exists($view_file);
    }

    public static function renderContent(array $data, $view_file) {
        self::$_render_started = true;
        extract($data);
        include($view_file);
        echo "\r\n";
    }

    public static function renderLayout() {
        require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'tpl.php');
        
        if (Router::getModule()!=DEFAULT_MODULE) {
            self::$body_class = Router::getModule().'-page';
        } else if (Router::getRequest() && Page::getRecordId()) {
            self::$body_class = 'record-page';
        } else if (Category::current()) {
            self::$body_class = 'category-page';
        } else if (!Router::getRequest() && Router::getModule()==DEFAULT_MODULE && Router::getController()==DEFAULT_CONTROLLER && Router::getAction()==DEFAULT_ACTION) {
            self::$body_class = 'home-page';
        } else {
            self::$body_class = 'zira-page';
        }
        
        $js_scripts = '';
        if (self::$_render_js_strings) {
            $js_scripts .= Helper::tag_open('script', array('type'=>'text/javascript'));
            $js_scripts .= 'zira_base = \''.Helper::baseUrl('').'\';';
            $js_scripts .= Helper::tag_close('script')."\r\n";
            
            $js_scripts .= Helper::tag_open('script', array('type'=>'text/javascript'));
            $js_scripts .= 'var zira_strings = { ';
            $co = 0;
            foreach(self::$_js_strings as $string => $translate) {
                if ($co>0) $js_scripts .= ', ';
                $js_scripts .= "'".Helper::html($string)."': ".json_encode(Helper::html(Locale::t($translate)));
                $co++;
            }
            $js_scripts .= ' };';
            $js_scripts .= Helper::tag_close('script')."\r\n";
        }
        
        if (!INSERT_SCRIPTS_TO_BODY) {
            self::addHTML($js_scripts, self::VAR_HEAD_BOTTOM);
        } else {
            $js_scripts = self::getLayoutData(self::VAR_SCRIPTS) . $js_scripts;
            self::$_body_bottom_scripts = array_merge(array($js_scripts), self::$_body_bottom_scripts);
        }

        if (!isset(self::$_layout_data[self::VAR_CHARSET])) self::$_layout_data[self::VAR_CHARSET] = CHARSET;
        self::$_layout_data[self::VAR_CHARSET] = Helper::tag_short('meta',array('charset'=>self::$_layout_data[self::VAR_CHARSET]))."\r\n";

        if (!isset(self::$_layout_data[self::VAR_TITLE])) {
            if (Config::get('site_title')) {
                self::$_layout_data[self::VAR_TITLE] = Locale::t(Config::get('site_title'));
            } else if (Config::get('site_name')) {
                self::$_layout_data[self::VAR_TITLE] = Locale::t(Config::get('site_name'));
            } else {
                self::$_layout_data[self::VAR_TITLE] = Locale::t(DEFAULT_TITLE);
            }
        }
        self::$_layout_data[self::VAR_TITLE] = Helper::tag('title',self::$_layout_data[self::VAR_TITLE])."\r\n";

        if (!self::$_keywords_added) {
            self::addMeta(array('name'=>'keywords','content'=>Locale::t(Config::get('site_keywords'))));
        }
        if (!self::$_description_added) {
            self::addMeta(array('name'=>'description','content'=>Locale::t(Config::get('site_description'))));
        }

        self::addCR();
        self::$_render_started = true;

        $theme_file = ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . self::getTheme() . DIRECTORY_SEPARATOR . 'theme.php';
        $render = include($theme_file);
        if ($render) {
            self::finishLayout();
        }
    }
    
    public static function finishLayout() {
        self::addThemeAssets();
        include(self::$layout);
    }

    public static function setRenderLayout($render_layout) {
        self::$_render_layout = (bool) $render_layout;
    }

    public static function setLayoutData(array $layout_data) {
        self::$_layout_data = array_merge(self::$_layout_data, $layout_data);
    }
    
    public static function &getLayoutDataArray() {
        return self::$_layout_data;
    }

    public static function addLayoutContent($placeholder, $content) {
        if (!isset(self::$_layout_data[$placeholder])) {
            self::$_layout_data[$placeholder] = $content."\r\n";
        } else {
            self::$_layout_data[$placeholder] .= $content."\r\n";
        }
    }

    public static function getLayoutData($var=null) {
        if ($var === null) return self::$_layout_data;
        if (!isset(self::$_layout_data[$var])) return null;
        return self::$_layout_data[$var];
    }

    public static function addPlaceholderView($placeholder,$data,$view) {
        if (!isset(self::$_placeholder_views[$placeholder])) self::$_placeholder_views[$placeholder] = array();
        self::$_placeholder_views[$placeholder][$view] = $data;
    }
    
    public static function getPlaceholderViews($placeholder) {
        if (!isset(self::$_placeholder_views[$placeholder])) return array();
        return self::$_placeholder_views[$placeholder];
    }
    
    public static function &getPlaceholderViewsArray() {
        return self::$_placeholder_views;
    }

    public static function includePlaceholderViews($placeholder) {
        if (!isset(self::$_placeholder_views[$placeholder]) || !is_array(self::$_placeholder_views[$placeholder])) return;

        foreach(self::$_placeholder_views[$placeholder] as $view=>$data) {
            self::renderView($data, $view);
        }
    }

    public static function addBodyBottomScript($script) {
        self::$_body_bottom_scripts []= $script;
    }

    public static function getBodyBottomScripts() {
        if (empty(self::$_body_bottom_scripts)) return '';
        return implode("\r\n", self::$_body_bottom_scripts);
    }
    
    public static function &getBodyBottomScriptsArray() {
        return self::$_body_bottom_scripts;
    }

    public static function getTheme() {
        return self::$_theme;
    }

    public static function setTheme($theme) {
        self::$_theme = $theme;
    }

    public static function getDefaultLayouts() {
        return array(
            self::LAYOUT_ALL_SIDEBARS => Locale::t('Layout with both left and right sidebars'),
            self::LAYOUT_LEFT_SIDEBAR => Locale::t('Layout with left sidebar'),
            self::LAYOUT_RIGHT_SIDEBAR => Locale::t('Layout with right sidebar'),
            self::LAYOUT_NO_SIDEBARS => Locale::t('Layout without sidebars')
        );
    }

    public static function getLayouts() {
        $layouts = self::getDefaultLayouts();

        $custom_layouts_dir = ROOT_DIR . DIRECTORY_SEPARATOR .
                            THEMES_DIR . DIRECTORY_SEPARATOR .
                            self::$_theme . DIRECTORY_SEPARATOR .
                            self::CUSTOM_LAYOUTS_FOLDER;

        if (file_exists($custom_layouts_dir) && is_dir($custom_layouts_dir) && is_readable($custom_layouts_dir)) {
            $d = opendir($custom_layouts_dir);
            while(($f=readdir($d))!==false) {
                if ($f=='.' || $f=='..' || is_dir($custom_layouts_dir . DIRECTORY_SEPARATOR . $f)) continue;
                if (substr($f, -4)!='.php') continue;
                $name = substr($f, 0, strlen($f)-4);
                $title = str_replace('-',' ', $name);
                $layouts[self::CUSTOM_LAYOUTS_FOLDER . '/' . $name] = Locale::t(ucfirst($title));
            }
            closedir($d);
        }

        return $layouts;
    }

    public static function addCR() {
        $c = Locale::t(Config::get('s'.'i'.  't'.'e'.'_'  .'c'.'o'."p"  .'y'.'r'.'i'  .'g'.'h'.'t'));
        if (!self::checkLK()) {
            self::addMeta(array('name'=>'gene'.  "rator",'content'=>'Zir'.  'a C'."MS"));
            if (!empty($c)) $c .= ' ';
            $s = 'P' . 'o' . "w" .  'e' . 'r' . 'e' .  'd' . ' ' . "b" .  'y' . ' ' . '%s';
            $t = Helper::tag('a', 'Z' . "i" .   'r' . 'a' . ' ' .  "C" . 'M' .  'S', array('href' => 'h'."t".'t'  .'p'.':'.'/'  .'/'.'d'."r"  .'o'.'1'.'d'  .'.'  .'r'."u"));
            $_t = '%tag%';
            $_c = Locale::t($s, $_t);
            if (strpos($_c, $_t)!==false) {
                $_c = str_replace($_t, $t, $_c);
            } else {
                $_c = str_replace('%s', $t, $s);
            }
            $c .= $_c;
            self::addLayoutContent(self::VAR_FOOTER, Helper::tag('script', 'zira_cr='.time().';', array('type'=>'text/javascript')));
        } else {
            self::$_body_bottom_scripts = array_merge(array(Helper::tag('script', 'zira_cr='.time().';', array('type'=>'text/javascript'))), self::$_body_bottom_scripts);
        }
        self::addHTML(Helper::tag_open('p').$c.Helper::tag_close('p'), self::VAR_FOOTER);
    }

    public static function checkLK() {
        $l = 'l'.'i'."c"  .'e'.'n'.    's'.'e';
        $k = 'k'.'e'.   "y".'.'.'p'   .'u'.'b';
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $l) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $l)) return false;
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $k) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $k)) return false;
        $lc = file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . $l);
        if (empty($lc)) return false;
        $kc = file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . $k);
        if (empty($kc)) return false;
        $pk = @call_user_func('o'.'p'.'e'  ."n".'s'.'s'  .'l'.'_'.'g'.'e'  .'t'.'_'.'p'.'u'  .'b'.'l'.'i'.'c'.'k'   .'e'.'y', $kc);
        if (!$pk) return false;
        $lcb = @call_user_func('b'.'a'."s".  'e'.'6'.'4'.  '_'.'d'.'e'  .'c'.'o'.  'd'.'e',$lc);
        if (!$lcb) return false;
        $lcd = null;
        $od = @call_user_func_array('o'."p".'e'  .'n'.'s'.'s'  .'l'.'_'.'p'  .'u'.'b'.'l'  .'i'.'c'.'_'.  'd'.'e'.'c'.  'r'.'y'.'p'  .'t',
            array($lcb, &$lcd, $pk)
        );
        if (!$od) return false;
        $h = 'H'.'T'.  'T'."P".  '_'.'H'.'O'  .'S'.'T';
        if (!isset($_SERVER[$h])) return false;
        if ($lcd != $_SERVER[$h] && '.'.$lcd != substr($_SERVER[$h], -(strlen($lcd)+1))) return false;
        return true;
    }

    public static function addBootstrap() {
        if (self::$_bootstrap_added) return;
        self::addStyle('bootstrap.min.css');
        self::addStyle('bootstrap-theme.min.css');
        self::addScript('bootstrap.min.js');

        self::registerRenderHook(get_called_class(), 'addHTML5ForIE');

        self::$_bootstrap_added = true;
    }
    
    public static function addHTML5ForIE() {
        $ie = '<!--[if lt IE 9]>';
        $ie .= '<script src="'.Helper::jsUrl('html5shiv.min.js').'"></script>';
        $ie .= '<script src="'.Helper::jsUrl('respond.min.js').'"></script>';
        $ie .= '<![endif]-->';

        self::addHTML($ie, self::VAR_HEAD_BOTTOM);
    }

    public static function addJquery() {
        if (self::$_jquery_added) return;
        self::addScript('jquery.min.js');
        self::$_jquery_added = true;
    }

    public static function addLightbox() {
        if (self::$_lightbox_added) return;
        self::addStyle('lightbox.css');
        $script = 'lightbox.min.js';
        if (!Assets::isActive() || !Assets::isMergedJS($script)) {
            //self::addHTML(Helper::tag('script', null, array('src'=>Helper::jsUrl($script))), self::VAR_BODY_BOTTOM);
            self::addBodyBottomScript(Helper::tag('script', null, array('src'=>Helper::jsUrl($script))));
        }
        self::$_lightbox_added = true;
    }

    public static function addSliderAssets() {
        if (self::$_slider_added) return;
        self::addStyle('bxslider.css');
        self::addScript('bxslider.min.js');
        self::$_slider_added = true;
    }

    public static function addSlider($id, array $options=null) {
        self::addSliderAssets();
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'jQuery(\'#'.Helper::html($id).'\').bxSlider({';
        if ($options) {
            $_options = array();
            foreach($options as $k=>$v) {
                if (is_bool($v)) {
                    $_options[]="'".Helper::html($k)."': ".($v ? 'true' : 'false');
                } else if (is_int($v)) {
                    $_options[]="'".Helper::html($k)."': ".Helper::html($v);
                } else {
                    $_options[]="'".Helper::html($k)."': '".Helper::html($v)."'";
                }
            }
            $script .= implode(', ',$_options);
        }
        $script .= '});';
        $script .= ' });';
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_HEAD_BOTTOM);
        self::addBodyBottomScript($script);
    }

    public static function addCropperAssets() {
        if (self::$_cropper_assets_added) return;
        self::addStyle('cropper.css');
        self::addScript('cropper.js');
        self::$_cropper_assets_added = true;
    }

    public static function addCropper($id, array $options=null) {
        self::addCropperAssets();
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'jQuery(\'img#'.Helper::html($id).'\').cropper({';
        if ($options) {
            $_options = array();
            foreach($options as $k=>$v) {
                if (is_bool($v)) {
                    $_options[]="'".Helper::html($k)."': ".($v ? 'true' : 'false');
                } else if (is_int($v)) {
                    $_options[]="'".Helper::html($k)."': ".Helper::html($v);
                } else {
                    $_options[]="'".Helper::html($k)."': '".Helper::html($v)."'";
                }
            }
            $script .= implode(', ',$_options);
        }
        $script .= '});';
        $script .= ' });';
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_HEAD_BOTTOM);
        self::addBodyBottomScript($script);
    }

    public static function addTinyMCEAssets() {
        if (self::$_tinymce_assets_added) return;
        if (Config::get('gzip')) {
            self::addScript('tinymce/tinymce.gzip.js');
        } else {
            self::addScript('tinymce/tinymce.min.js');
        }
        self::$_tinymce_assets_added = true;
    }

    public static function addTinyMCE($id) {
        self::addTinyMCEAssets();
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'tinymce.init({'.
                    'selector:\'#'.$id.'\' ,'.
                    'plugins: \'paste, advlist, link, image, media, table, hr, pagebreak, code, contextmenu\','.
                    'toolbar: [\'undo redo | table | bullist numlist | image media link | outdent indent | hr pagebreak | code\', \'styleselect | bold italic underline removeformat |  aligncenter alignleft alignright alignjustify \'],'.
                    'menubar: false,'.
                    'language: \''.Locale::getLanguage().'\','.
                    'convert_urls: false,'.
                    'paste_word_valid_elements: \'b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li,hr,br,table,tr,td\','.
                    'paste_filter_drop: false,'.
                    'init_instance_callback: function (editor) {'.
                    '$(editor.getDoc()).unbind(\'drop\').bind(\'drop\',function(e){e.stopPropagation();e.preventDefault();});'.
                    '},'.
                    'inline: true'.
                    '});';
        $script .= ' });';
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_HEAD_BOTTOM);
        self::addBodyBottomScript($script);
    }

    public static function addDatepickerAssets() {
        if (self::$_datepicker_assets_added) return;
        self::addStyle('bootstrap-datetimepicker.min.css');
        self::addScript('moment.min.js');
        if (Locale::getLanguage()=='ru') self::addScript('moment-locale-ru.js');
        else if (Locale::getLanguage()!='en' && file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'moment-locale-' . Locale::getLanguage() . '.js')) {
            self::addScript('moment-locale-'.Locale::getLanguage().'.js');
        }
        self::addScript('bootstrap-datetimepicker.min.js');
        self::$_datepicker_assets_added = true;
    }

    /**
     * @param string $viewMode - accepts 'decades','years','months','days'
     * @param null $maxDate - format 'Y-m-d'
     */
    public static function addDatepicker($viewMode = null, $maxDate = null) {
        if (self::$_datepicker_added) return;
        self::addDatepickerAssets();
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= "zira_datepicker = function(element){";
        $script .= "jQuery(element).datetimepicker({";
        $options = array();
        if ($viewMode!==null) $options[]="viewMode: '".$viewMode."'";
        if (Locale::getLanguage()=='ru') $options[]="locale: 'ru'";
        $options[]="allowInputToggle: true";
        $options[]="format: '".Config::get('datepicker_date_format')."'";
        if ($maxDate!==null) $options[]="maxDate: '".$maxDate."'";
        $script .= implode(', ', $options);
        $script .= "});";
        $script .= "};";
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_BODY_BOTTOM);
        self::addBodyBottomScript($script);
        self::$_datepicker_added = true;
    }
    
    public static function addCodeMirrorAssets() {
        if (self::$_codemirror_assets_added) return;
        self::addStyle('codemirror.css');
        self::$_codemirror_assets_added = true;
    }

    public static function addCodeMirror() {
        if (self::$_codemirror_added) return;
        self::addCodeMirrorAssets();
        $script = 'codemirror/index.php';
        $mtime = filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $script));
        if (Config::get('clean_url')) {
            $script = 'cm';
        }
        $script_url = $script . '?t=' . (intval(Assets::isGzipEnabled())+1).$mtime;
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= "zira_codemirror_init = function(element, cm_mode){";
        $script .= "if (typeof(cm_mode)=='undefined') cm_mode = 'htmlmixed';";
        $script .= "return CodeMirror.fromTextArea(jQuery(element).get(0), { mode: cm_mode, inputStyle: 'contenteditable', viewportMargin: Infinity, scrollbarStyle: 'simple' });";
        $script .= "};";
        $script .= "zira_codemirror = function(element, mode){";
        $script .= "var cm = {};";
        $script .= "if (typeof CodeMirror == 'undefined') {";
        $script .= "var script = document.createElement('script');";
        $script .= "script.onload = function() {";
        $script .= "cm.editor = zira_codemirror_init(element, mode);";
        $script .= "cm.editor.on('change', function(){ if (typeof(cm.change)!='undefined') cm.change.call(); });";
        $script .= "};";
        $script .= "script.src = '".Helper::jsUrl($script_url)."';";
        $script .= "document.body.appendChild(script);";
        $script .= "} else {";
        $script .= "cm.editor = zira_codemirror_init(element, mode);";
        $script .= "cm.editor.on('change', function(){ if (typeof(cm.change)!='undefined') cm.change.call(); });";
        $script .= "}";
        $script .= "return cm;";
        $script .= "};";
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_BODY_BOTTOM);
        self::addBodyBottomScript($script);
        self::$_codemirror_added = true;
    }
    
    public static function addJPlayerAssets() {
        if (self::$_jplayer_assets_added) return;
        self::addStyle('jplayer.css');
        //self::addScript('jplayer/jquery.jplayer.min.js');
        //self::addScript('jplayer/jplayer.playlist.min.js');
        self::addScript('jplayer/jplayer-and-playlist.min.js');
        self::$_jplayer_assets_added = true;
    }
    
    public static function addJPlayer($container_id, $player_id, array $files, $media_type, $poster = null) {
        if (empty($container_id) || empty($player_id) || empty($files)) return;
        if ($media_type != 'audio' && $media_type != 'video') return;
        $formats = array();
        $playlist = self::_generateJPlayerPlaylist($files, $media_type, $formats, $poster);
        if (empty($playlist)) return;
        self::addJPlayerAssets();
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        if ($media_type == 'audio') {
            $script .= 'jQuery(\'#'.Helper::html($container_id).'\').parents(\'.jplayer-audio-wrapper\').css(\'display\',\'block\');';
        } else if ($media_type == 'video') {
            $script .= 'jQuery(\'#'.Helper::html($container_id).'\').parents(\'.jplayer-video-wrapper\').css(\'display\',\'block\');';
        }
        $script .= 'new jPlayerPlaylist({';
        $script .= 'jPlayer: \'#'.Helper::html($player_id).'\',';
        $script .= 'cssSelectorAncestor: \'#'.Helper::html($container_id).'\'';
	$script .= '},';
        $script .= $playlist;
        $script .= ', {';
        $script .= 'swfPath: \''.Helper::jsUrl('jplayer/jquery.jplayer.swf').'\',';
        $script .= 'errorAlerts: false,';
        /*
        if ($media_type == 'audio') {
            $script .= 'supplied: \'webma, oga, mp3, m4a, fla\',';
        } else if ($media_type == 'video') {
            $script .= 'supplied: \'webmv, ogv, m4v, flv\',';
        }
         */
        $script .= 'supplied: \''. implode(',', $formats).'\',';
        $script .= 'useStateClassSkin: true,';
        $script .= 'autoBlur: false,';
        $script .= 'smoothPlayBar: false,';
        $script .= 'keyEnabled: false,';
        if ($media_type == 'video') {
            $script .= 'size: {';
            $script .= 'width: "100%",';
            $script .= 'height: "400px"';
            $script .= '},';
        }
        $script .= 'remainingDuration: true';
	$script .= '});';
        $script .= '});';
        $script .= Helper::tag_close('script');
        self::addBodyBottomScript($script);
    }
    
    protected static function _generateJPlayerPlaylist($files, $media_type, &$formats, $poster = null) {
        $media = array();
        foreach($files as $file) {
            if (!empty($file->embed)) continue;
            
            $media_str = '{';
            if (!empty($file->description)) {
                $media_str .= 'title:\''.Helper::html($file->description).'\',';
            } else if (!empty($file->path)) {
                $media_str .= 'title:\''.Helper::html(basename($file->path)).'\',';
            } else if (!empty($file->url)) {
                $media_str .= 'title:\''.Helper::html(basename($file->url)).'\',';
            }
            
            if ($media_type == 'video' && !empty($poster)) {
                $media_str .= 'poster:\''.Helper::baseUrl(Helper::html($poster)).'\',';
            }
            
            if (!empty($file->path)) {
                $url = Helper::baseUrl($file->path);
            } else if (!empty($file->url)) {
                $url = $file->url;
            }
            $format = '';
            $p = strrpos($url, '.');
            if ($p!==false) {
                $ext = substr($url, $p+1);
                if ($ext == 'mp3' || $ext == 'm4a' || $ext == 'oga' || $ext == 'webma' || $ext == 'fla' || 
                    $ext == 'm4v'|| $ext == 'ogv' || $ext == 'webmv' || $ext == 'flv'
                ) {
                    $format = $ext;
                }
                if ($ext == 'ogg' && $media_type=='audio') $format = 'oga';
                else if ($ext == 'ogg' && $media_type=='video') $format = 'ogv';
                if ($ext == 'mp4' && $media_type=='audio') $format = 'm4a';
                else if ($ext == 'mp4' && $media_type=='video') $format = 'm4v';
                if ($ext == 'webm' && $media_type=='audio') $format = 'webma';
                else if ($ext == 'webm' && $media_type=='video') $format = 'webmv';
            }
            if (empty($format)) continue;
    
            if (!in_array($format, $formats)) $formats []= $format;
            
            $media_str .= $format.':\''.Helper::html($url).'\'';
            $media_str .= '}';
            $media []= $media_str;
        }
        
        if (empty($media)) return;
        
        $playlist = '[';
        $playlist .= implode(',', $media);
        $playlist .= ']';
        
        return $playlist;
    }
    
    public static function addColorpickerAssets() {
        if (self::$_colorpicker_assets_added) return;
        self::addStyle('bootstrap-colorpicker.min.css');
        self::addScript('bootstrap-colorpicker.min.js');
        self::$_colorpicker_assets_added = true;
    }

    public static function addColorpicker() {
        if (self::$_colorpicker_added) return;
        self::addColorpickerAssets();
        $script = Helper::tag_open('script',array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'jQuery(\'.zira-colorpicker\').each(function(){';
        $script .= 'jQuery(this).colorpicker().on(\'changeColor\', zira_bind(this, function(e) {';
        $script .= 'var color = e.color.toString(\'rgba\');';
        $script .= 'jQuery(this).data(\'color\', color);';
        $script .= 'jQuery(this).trigger(\'change\');';
        $script .= '}));';
        $script .= '});';
        $script .= '});';
        $script .= Helper::tag_close('script');
        //self::addHTML($script, self::VAR_HEAD_BOTTOM);
        self::addBodyBottomScript($script);
        self::$_colorpicker_added = true;
    }

    public static function addAutoCompleter() {
        if (self::$_autocomplete_added) return;
        self::addScript('autocomplete.js');
        self::$_autocomplete_added = true;
    }

    public static function addParser() {
        if (self::$_parser_added) return;
        self::addScript('parse.js');
        self::$_parser_added = true;
        self::addLightbox();
    }

    public static function addCoreStyles() {
        self::addStyle('zira.css');
    }

    public static function addCoreScripts() {
        self::addScript('zira.js');
    }

    public static function addThemeStyles() {
        if (Assets::isActive()) return;
        $css = 'main.css';
        if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . CSS_DIR . DIRECTORY_SEPARATOR .$css)) {
            self::addThemeStyle($css);
        }
    }

    public static function addThemeScripts() {
        $script = 'main.js';
        if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR .$script)) {
            self::addThemeScript($script);
        }
    }

    public static function addCoreAssets() {
        if (self::$_core_assets_added) return;
        self::addCoreStyles();
        self::addCoreScripts();
        self::$_core_assets_added = true;
    }

    public static function addThemeAssets() {
        if (self::$_theme_assets_added) return;
        self::addThemeStyles();
        self::addThemeScripts();
        self::$_theme_assets_added = true;
    }

    public static function addDefaultAssets() {
        self::addJquery();
        self::addBootstrap();
        self::addCoreAssets();
    }

    public static function preloadLoader() {
        if (self::$_loader_preloaded) return;
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'var loader = new Image();';
        $script .= 'loader.src = \''.Helper::imgUrl('loader.gif').'\';';
        $script .= ' });';
        $script .= Helper::tag_close('script');
        //View::addHTML($script, View::VAR_HEAD_BOTTOM);
        self::addBodyBottomScript($script);
        self::$_loader_preloaded = true;
    }

    public static function addWidget($class) {
        self::$_widgets[]=$class;
    }

    public static function addDbWidget($row, $placeholder) {
        if (!isset(self::$_db_widgets[$placeholder])) self::$_db_widgets[$placeholder] = array();
        self::$_db_widgets[$placeholder][]=$row;
    }

    public static function setWidgets(array $widgets) {
        self::$_widgets=$widgets;
    }
    
    public static function &getWidgetsArray() {
        return self::$_widgets;
    }
    
    public static function &getDbWidgetsArray() {
        return self::$_db_widgets;
    }

    public static function setRenderWidgets($render_widgets) {
        self::$_render_widgets = (bool) $render_widgets;
    }

    public static function setRenderDbWidgets($render_db_widgets) {
        self::$_render_db_widgets = (bool) $render_db_widgets;
    }

    public static function prepareWidgets() {
        self::$_widget_objects = array();
        $objects = array();
        $placeholders = array();
        $orders = array();
        $i=0;
        foreach(self::$_widgets as $class) {
            try {
                if (is_object($class)) {
                    $widget = $class;
                } else {
                    $widget = new $class;
                }
                if (!($widget instanceof Widget)) continue;
                $orders[$i] = $widget->getOrder();
                $placeholders[$i] = $widget->getPlaceholder();
                $objects[$i] = $widget;
                $i++;
            } catch(\Exception $e) {
                if (defined('DEBUG') && DEBUG) throw $e;
                else Log::exception($e);
            }
        }
        asort($orders);
        foreach($orders as $i=>$order) {
            if (!isset($objects[$i]) || !isset($placeholders[$i])) continue;
            if (!isset(self::$_widget_objects[$placeholders[$i]])) {
                self::$_widget_objects[$placeholders[$i]] = array();
            }
            self::$_widget_objects[$placeholders[$i]][]=$objects[$i];
        }
    }

    public static function prepareDbWidgets($placeholder) {
        if (empty(self::$_db_widgets[$placeholder])) return;
        self::$_db_widget_objects = array();
        if (!self::$_render_db_widgets) {
            $defaultDbWidgets = Widgets::getDefaultDbWidgets();
        }
        foreach(self::$_db_widgets[$placeholder] as $_widget) {
            try {
                if (!self::$_render_db_widgets && !in_array($_widget->name, $defaultDbWidgets)) continue;
                if ($_widget->record_id && $_widget->record_id!=Page::getRecordId()) continue;
                if ($_widget->url && strlen($_widget->url)>0) {
                    $request = urldecode(Router::getRequest());
                    if ($_widget->url != $request && (
                        strlen($_widget->url)<=2 || 
                        substr($_widget->url, -2) != '/*' 
                    )) continue;
                    if ($_widget->url != $request && ( 
                        strlen($_widget->url)<=2 ||
                        substr($_widget->url, -2) != '/*' ||
                        mb_strpos($request.'/', substr($_widget->url, 0, strlen($_widget->url)-1), 0, CHARSET)!==0
                    )) continue;
                }
                if ($_widget->filter && ((
                    $_widget->filter == Models\Widget::STATUS_FILTER_RECORD &&
                    Page::getRecordId()===null
                ) || (
                    $_widget->filter == Models\Widget::STATUS_FILTER_CATEGORY &&
                    (!Category::current() || Category::param() || Page::getRecordId()!==null)
                ) || (
                    $_widget->filter == Models\Widget::STATUS_FILTER_CATEGORY_AND_RECORD &&
                    !Category::current() && Page::getRecordId()===null
                ))) {
                    continue;
                }
                $widget = new $_widget->name;
                if (!($widget instanceof Widget)) continue;
                $widget->setData($_widget->params);
                $widget->setPlaceholder($_widget->placeholder);
                $widget->setOrder($_widget->sort_order);
                if (!isset(self::$_db_widget_objects[$_widget->placeholder])) {
                    self::$_db_widget_objects[$_widget->placeholder] = array();
                }
                self::$_db_widget_objects[$_widget->placeholder][]=$widget;
            } catch(\Exception $e) {
                if (defined('DEBUG') && DEBUG) throw $e;
                else Log::exception($e);
            }
        }
    }

    public static function renderWidgets($placeholder) {
        if (!self::$_render_widgets) return;
        self::renderCoreWidgets($placeholder);
        self::renderDbWidgets($placeholder);
    }

    public static function renderCoreWidgets($placeholder) {
        if (self::$_widget_objects === null) {
            self::prepareWidgets();
        }
        if (!isset(self::$_widget_objects[$placeholder]) ||
            !is_array(self::$_widget_objects[$placeholder])) return;

        foreach(self::$_widget_objects[$placeholder] as $widget) {
            try {
                if (!($widget instanceof Widget)) continue;
                $widget->render();
            } catch(\Exception $e) {
                if (defined('DEBUG') && DEBUG) throw $e;
                else Log::exception($e);
            }
        }
        unset(self::$_widget_objects[$placeholder]);
    }

    public static function renderDbWidgets($placeholder) {
        if (self::$_db_widget_objects === null || !isset(self::$_db_widget_objects[$placeholder])) {
            self::prepareDbWidgets($placeholder);
        }
        if (!isset(self::$_db_widget_objects[$placeholder]) ||
            !is_array(self::$_db_widget_objects[$placeholder])) return;

        foreach(self::$_db_widget_objects[$placeholder] as $widget) {
            try {
                if (!($widget instanceof Widget)) continue;
                $widget->render();
            } catch(\Exception $e) {
                if (defined('DEBUG') && DEBUG) throw $e;
                else Log::exception($e);
            }
        }
        unset(self::$_db_widget_objects[$placeholder]);
    }
}
