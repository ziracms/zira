<?php
/**
 * Zira project.
 * dash.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash;

use Zira;

class Dash {
    const PANEL_GROUP_WEBSITE= 'Website';
    const PANEL_GROUP_SYSTEM = 'System';
    const PANEL_GROUP_SETTINGS = 'Settings';
    const PANEL_GROUP_MODULES = 'Modules';

    const GET_FRAME_PARAM = 'referer';
    const GET_FRAME_VALUE = 'dash';

    const SCRIPT_PARTIAL_LIMIT = 30;

    const TOKEN_NAME = 'dash-token';
    const COOKIE_NAME = 'dash-cookie';

    const CONFIG_FRONTEND_PANEL_ENABLED = 'dash_panel_frontend';

    const NOTIFICATION_HOOK = 'dash_notifications';

    private static $_instance;
    protected $_styles = array();
    protected $_scripts = array();
    protected $_panel_items = array();
    protected $_panel_website_group;
    protected $_panel_system_group;
    protected $_panel_settings_group;
    protected $_panel_modules_group;
    protected $_panel_callbacks = array();
    protected $_panel_items_co = 0;
    protected $_panel_items_id_prefix = 'dashpanel-menu-item-';
    protected $_windows = array();
    protected $_models = array();
    protected $_js_names = array();
    protected $_includes = array();
    protected $_strings = array();
    protected $_vars = array();

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function addStyle($style) {
        $this->_styles[]=$style;
    }

    public function addScript($script) {
        $this->_scripts[]=$script;
    }

    public function addPanelGroup(array $group) {
        $this->_panel_items[]=$group;
    }

    public function addPanelItem($icon_class, $title, $url) {
        $this->_panel_items_co++;
        $this->_panel_items[]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
    }

    public function createPanelGroup($icon_class, $title) {
        $this->_panel_items_co++;
        return array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => array()
        );
    }

    public function addPanelGroupItem(array $group, $icon_class, $title, $url, $js = null) {
        $this->_panel_items_co++;
        if (!empty($js)) $this->_panel_callbacks[$this->_panel_items_id_prefix.$this->_panel_items_co] = $js;
        $group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
        return $group;
    }

    public function addPanelGroupSeparator(array $group) {
        $this->_panel_items_co++;
        $group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'type' => 'separator'
        );
        return $group;
    }

    protected function addPanelWebsiteGroupItem($icon_class, $title, $url, $js = null) {
        if ($this->_panel_website_group===null) {
            $this->_panel_website_group = $this->createPanelGroup('glyphicon glyphicon-link', Zira\Locale::t(self::PANEL_GROUP_WEBSITE));
        }
        $this->_panel_items_co++;
        if (!empty($js)) $this->_panel_callbacks[$this->_panel_items_id_prefix.$this->_panel_items_co] = $js;
        $this->_panel_website_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
    }

    public function addPanelWebsiteGroupModuleItem($icon_class, $title, $url, $js = null) {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelWebsiteGroupItem($icon_class, $title, $url, $js);
    }

    protected function addPanelWebsiteGroupSeparator() {
        if ($this->_panel_website_group===null) {
            $this->_panel_website_group = $this->createPanelGroup('glyphicon glyphicon-link', Zira\Locale::t(self::PANEL_GROUP_WEBSITE));
        }
        $this->_panel_items_co++;
        $this->_panel_website_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'type' => 'separator'
        );
    }

    public function addPanelWebsiteGroupModuleSeparator() {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelWebsiteGroupSeparator();
    }

    protected function addPanelSystemGroupItem($icon_class, $title, $url, $js = null) {
        if ($this->_panel_system_group===null) {
            $this->_panel_system_group = $this->createPanelGroup('glyphicon glyphicon-tasks', Zira\Locale::t(self::PANEL_GROUP_SYSTEM));
        }
        $this->_panel_items_co++;
        if (!empty($js)) $this->_panel_callbacks[$this->_panel_items_id_prefix.$this->_panel_items_co] = $js;
        $this->_panel_system_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
    }

    public function addPanelSystemGroupModuleItem($icon_class, $title, $url, $js = null) {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelSystemGroupItem($icon_class, $title, $url, $js);
    }

    protected function addPanelSystemGroupSeparator() {
        if ($this->_panel_system_group===null) {
            $this->_panel_system_group = $this->createPanelGroup('glyphicon glyphicon-tasks', Zira\Locale::t(self::PANEL_GROUP_SYSTEM));
        }
        $this->_panel_items_co++;
        $this->_panel_system_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'type' => 'separator'
        );
    }

    public function addPanelSystemGroupModuleSeparator() {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelSystemGroupSeparator();
    }

    protected function addPanelSettingsGroupItem($icon_class, $title, $url, $js = null) {
        if ($this->_panel_settings_group===null) {
            $this->_panel_settings_group = $this->createPanelGroup('glyphicon glyphicon-cog', Zira\Locale::t(self::PANEL_GROUP_SETTINGS));
        }
        $this->_panel_items_co++;
        if (!empty($js)) $this->_panel_callbacks[$this->_panel_items_id_prefix.$this->_panel_items_co] = $js;
        $this->_panel_settings_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
    }

    public function addPanelSettingsGroupModuleItem($icon_class, $title, $url, $js = null) {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelSettingsGroupItem($icon_class, $title, $url, $js);
    }

    protected function addPanelSettingsGroupSeparator() {
        if ($this->_panel_settings_group===null) {
            $this->_panel_settings_group = $this->createPanelGroup('glyphicon glyphicon-cog', Zira\Locale::t(self::PANEL_GROUP_SETTINGS));
        }
        $this->_panel_items_co++;
        $this->_panel_settings_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'type' => 'separator'
        );
    }

    public function addPanelSettingsGroupModuleSeparator() {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        $this->addPanelSettingsGroupSeparator();
    }

    public function addPanelModulesGroupItem($icon_class, $title, $url, $js = null) {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        if ($this->_panel_modules_group===null) {
            $this->_panel_modules_group = $this->createPanelGroup('glyphicon glyphicon-th-large', Zira\Locale::t(self::PANEL_GROUP_MODULES));
        }
        $this->_panel_items_co++;
        if (!empty($js)) $this->_panel_callbacks[$this->_panel_items_id_prefix.$this->_panel_items_co] = $js;
        $this->_panel_modules_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'icon_class' => $icon_class,
            'label' => $title,
            'rel' => $url
        );
    }

    public function addPanelModulesGroupSeparator() {
        if ($this->isPanelItemsLowMemoryModeEnabled()) return;
        if ($this->_panel_modules_group===null) {
            $this->_panel_modules_group = $this->createPanelGroup('glyphicon glyphicon-th-large', Zira\Locale::t(self::PANEL_GROUP_MODULES));
        }
        $this->_panel_items_co++;
        $this->_panel_modules_group['rel'][]=array(
            'id' => $this->_panel_items_id_prefix.$this->_panel_items_co,
            'type' => 'separator'
        );
    }

    public function addPanelDefaultGroups() {
        if (!empty($this->_panel_website_group['rel'])) {
            $this->addPanelGroup($this->_panel_website_group);
        }
        if (!empty($this->_panel_system_group['rel'])) {
            $this->addPanelGroup($this->_panel_system_group);
        }
        if (!empty($this->_panel_settings_group['rel'])) {
            $this->addPanelGroup($this->_panel_settings_group);
        }
        if (!empty($this->_panel_modules_group['rel'])) {
            $this->addPanelGroup($this->_panel_modules_group);
        }
    }

    public function getStyles() {
        return $this->_styles;
    }

    public function getScripts() {
        return $this->_scripts;
    }

    public function getPanelItems() {
        return $this->_panel_items;
    }

    public function getPanelCallbacks() {
        return $this->_panel_callbacks;
    }

    public static function isFrame() {
        return Zira\Request::get(self::GET_FRAME_PARAM)==self::GET_FRAME_VALUE;
    }

    protected static function addViewStyle($url) {
        $attributes = array();
        if (!isset($attributes['rel'])) $attributes['rel'] = 'stylesheet';
        if (!isset($attributes['type'])) $attributes['type'] = 'text/css';
        if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . Zira\View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . CSS_DIR . DIRECTORY_SEPARATOR .$url)) {
            $attributes['href'] = rtrim(BASE_URL,'/') . '/' . THEMES_DIR . '/' . Zira\View::getTheme() . '/' . ASSETS_DIR . '/' . CSS_DIR . '/' .$url;
        } else {
            $attributes['href'] = rtrim(BASE_URL,'/') . '/' . THEMES_DIR . '/' . DEFAULT_THEME . '/' . ASSETS_DIR . '/' . CSS_DIR . '/' .$url;
        }
        Zira\View::addHTML(Zira\Helper::tag_short('link', $attributes),Zira\View::VAR_STYLES);
    }

    protected static function addViewScript($url) {
        $attributes = array();
        if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . THEMES_DIR . DIRECTORY_SEPARATOR . Zira\View::getTheme() . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR .$url)) {
            $attributes['src'] = rtrim(BASE_URL,'/') . '/' . THEMES_DIR . '/' . Zira\View::getTheme() . '/' . ASSETS_DIR . '/' . JS_DIR . '/' .$url;
        } else {
            $attributes['src'] = rtrim(BASE_URL,'/') . '/' . THEMES_DIR . '/' . DEFAULT_THEME . '/' . ASSETS_DIR . '/' . JS_DIR . '/' .$url;
        }
        Zira\View::addHTML(Zira\Helper::tag('script', null, $attributes),Zira\View::VAR_SCRIPTS);
    }

    protected function registerWindowClass($js_name, $class, $model = null) {
        $this->_windows[$js_name]=$class;
        $this->_js_names[$class]=$js_name;
        if ($model!==null) $this->_models[$js_name]=$model;
    }

    public function registerModuleWindowClass($js_name, $class, $model = null) {
        if ($this->isWindowLowMemoryModeEnabled()) return;
        $this->registerWindowClass($js_name, $class, $model);
    }

    public function getWindowClass($js_name) {
        if (empty($js_name)) return null;
        if (!array_key_exists($js_name, $this->_windows)) return null;
        return $this->_windows[$js_name];
    }

    public function getModelClass($js_name) {
        if (empty($js_name)) return null;
        if (!array_key_exists($js_name, $this->_models)) return null;
        return $this->_models[$js_name];
    }

    public function getWindowJSName($class) {
        if (empty($class)) return null;
        if (!array_key_exists($class, $this->_js_names)) return null;
        return $this->_js_names[$class];
    }

    public function registerWindowScript($path) {
        if (in_array($path, $this->_includes)) return;
        $this->_includes []= $path;
    }

    public function registerWindowString($str) {
        if (in_array($str, $this->_strings)) return;
        $this->_strings []= $str;
    }

    public function registerWindowVariable($name,$value) {
        if (array_key_exists($name, $this->_vars)) throw new \Exception('Variable '.Zira\Helper::html($name).' already registered');
        $this->_vars[$name]=$value;
    }

    public function registerWindowVariableOnce($name,$value) {
        if (array_key_exists($name, $this->_vars)) return;
        $this->_vars[$name]=$value;
    }

    public function getRenderedWindowsJS($page = 0) {
        if ($page>0) {
            $offset = self::SCRIPT_PARTIAL_LIMIT * ($page-1);
        }
        $co=0;
        $script = '';
        foreach($this->_windows as $js_name=>$class) {
            $co++;
            if (isset($offset) && $co<=$offset) continue;
            $wnd = new $class();
            $wnd->build();
            $script .= $wnd->render();
            unset($wnd);
            if (isset($offset) && $co>=$offset+self::SCRIPT_PARTIAL_LIMIT) break;
        }
        return $script;
    }

    public function getWindowsIncludesJS() {
        $js = '';
        foreach ($this->_strings as $index => $str) {
            $js .= "desk_strings['" . Zira\Helper::html($str) . "']=" . json_encode(Zira\Helper::html(Zira\Locale::t($str))).";\r\n";
        }
        foreach ($this->_vars as $var => $val) {
            $js .= "var " . Zira\Helper::html($var) . " = " . json_encode(Zira\Helper::html($val)) . ";\r\n";
        }
        foreach ($this->_includes as $path) {
            if (strpos($path, '..') !== false) continue;
            $js .= file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . $path . '.js') . "\r\n";
        }
        return $js;
    }

    protected function getDashPredefinedVars() {
        $js = 'var desk_url = \'' . Zira\Helper::url('') . '\';' . "\r\n";
        $js .= 'var desk_base = \'' . Zira\Helper::baseUrl('') . '\';' . "\r\n";
        $js .= 'var desk_token = \'' . self::getToken() . '\';' . "\r\n";
        $js .= 'var desk_ds = \'\\' . DIRECTORY_SEPARATOR . '\';' . "\r\n";
        $js .= 'var desk_strings = {};'. "\r\n";
        return $js;
    }

    public function getRenderScript($renderWindows = true) {
        $js ='';
        if ($renderWindows && !$this->isReferedFromDash()) {
            $js .= '(function($){' . "\r\n";
        }
        $js .= 'if (typeof(HTMLElement)!="undefined") {'."\r\n";
        $js .= 'var HTMLElementClick = HTMLElement.prototype.click;'."\r\n";
        $js .= 'HTMLElement.prototype.click = null;'."\r\n";
        $js .= 'HTMLElement.prototype.dispatchEvent = null;'."\r\n";
        $js .= '}'."\r\n";
        $js .= $this->getDashPredefinedVars();
        $js .= file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk-window.js')."\r\n";
        $js .= file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk.js')."\r\n";
        $js .= file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk-wrapper.js')."\r\n";
        if ($renderWindows) {
            $js .= $this->getRenderedWindowsJS();
            $js .= $this->getWindowsIncludesJS();
        }
        $js .= '$(document).ready(function(){';
        foreach($this->getPanelCallbacks() as $id=>$script) {
            $script = trim($script);
            if (substr($script,-1)!=';') $script .= ';';
            $js .= '$(\'#'.$id.'\').click(function(e){';
            $js .= $script;
            $js .= '});';
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) && Zira\Permission::check(Zira\Permission::TO_EDIT_RECORDS)) {
            $js .= 'if ($(\'.editor-links-wrapper\').length>0){';
            $js .= '$(\'.editor-links-wrapper\').addClass(\'active\');';
            $js .= '$(\'.editor-links-wrapper\').children(\'.category\').click(desk_editor_category_callback);';
            $js .= '$(\'.editor-links-wrapper\').children(\'.record\').click(desk_editor_record_callback);';
            $js .= '}';
        }
        $js .= '});'."\r\n";
        $js .= 'window.setInterval("dashPinger=$.get(\''.Zira\Helper::url('dash/index/ping').'?'.FORMAT_GET_VAR.'='.FORMAT_JSON.'\').always(function(xhr){if (dashPinger.status!=200) jQuery(\'#dashpanel-container nav\').addClass(\'disabled\'); else $(\'#dashpanel-container nav\').removeClass(\'disabled\'); });",600000);'."\r\n"; // keep session alive
        if (defined('DEBUG') && DEBUG) {
            $js .= 'DeskDebug = Desk;'."\r\n";
        }
        $js .= 'if (typeof(HTMLElement)=="undefined" || typeof(FormData)=="undefined") {'."\r\n";
        $js .= 'window.setTimeout("zira_error(t(\'Sorry, but it seems that your browser is not supported.\'));", 1000);'."\r\n";
        $js .= '}'."\r\n";
        if ($renderWindows && !$this->isReferedFromDash()) {
            $js .= '})(jQuery);' . "\r\n";
        }
        return $js;
    }

    public function getRenderScriptPartial() {
        $pages = ceil(count($this->_windows) / self::SCRIPT_PARTIAL_LIMIT);
        $js = '(function($){'."\r\n";
        $js .= 'var dash_script_partial_pages = '.$pages.';'."\r\n";
        $js .= '$(document).ready(function(){';
        $js .= 'for (var i=1; i<=dash_script_partial_pages; i++){';
        $js .= '$.get(\''.Zira\Helper::url('dash/jsp').'?p='.'\'+i, function(response){';
        $js .= 'eval(response);';
        $js .= '});';
        $js .= '}';
        $js .= '});'."\r\n";
        $js .= '})(jQuery);'."\r\n";
        return $js;
    }

    public function getRenderScriptETag() {
        $etag = self::getToken();
        $etag .= Zira\Config::get('config_version');
        $etag .= Zira\Locale::getLanguage();
        $etag .= ((defined('DEBUG') && DEBUG) ? 'debug' : 'production');
        foreach($this->_windows as $js_name=>$class) {
            $etag .= $js_name;
        }
        $etag .= filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk-window.js');
        $etag .= filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk.js');
        $etag .= filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . JS_DIR . DIRECTORY_SEPARATOR . 'desk-wrapper.js');
        return md5($etag);
    }

    /**
     * Used only in dash layout
     */
    public function renderPanel() {
        if (self::isFrame()) return;
        $panelWidget = new Widgets\Panel();
        $panelWidget->render();
    }

    public function addFrameJs() {
        $js = Zira\Helper::tag_open('script',array('type'=>'text/javascript'));
        $js .= '(function($){';
        $js .= '$(document).ready(function(){';
        $js .= '$(\'a\').each(function(){';
        $js .= 'var href = $(this).attr(\'href\');';
        $js .= 'if (href && href!=\'javascript:void(0)\' && href.indexOf(\'//\')<0) {';
        $js .= '$(this).click(function(e){';
        $js .= 'if (href.indexOf(\'?\')<0) href += \'?\';';
        $js .= 'else href += \'&\';';
        $js .= 'href += \''.self::GET_FRAME_PARAM.'='.self::GET_FRAME_VALUE.'\';';
        $js .= '$(this).attr(\'href\',href);';
        $js .= '});';
        $js .= '}';
        $js .= '});';
        $js .= '});';
        $js .= '})(jQuery);';
        $js .= Zira\Helper::tag_close('script');
        Zira\View::addHTML($js, Zira\View::VAR_BODY_BOTTOM);
    }

    public static function forbidden() {
        http_response_code(Zira\Response::STATUS_403);
        exit;
    }

    public static function generateToken() {
        $random = Zira::randomSecureString(8);
        return 'dash-'.$random;
    }

    public static  function getToken() {
        $exist = Zira\Session::get(self::TOKEN_NAME);
        if ($exist) return $exist;

        $token = self::generateToken();
        Zira\Session::set(self::TOKEN_NAME,$token);

        return $token;
    }

    public static  function checkToken($token) {
        if (!$token) return false;
        $exist = Zira\Session::get(self::TOKEN_NAME);
        if (!$exist) return false;

        return ($token == $exist);
    }

    public static  function clearToken() {
        Zira\Session::remove(self::TOKEN_NAME);
    }

    public static  function getCookie() {
        $exist = Zira\Session::get(self::COOKIE_NAME);
        if ($exist) return $exist;

        $token = self::generateToken();
        Zira\Session::set(self::COOKIE_NAME,$token);

        return $token;
    }

    public static  function checkCookie($token) {
        if (!$token) return false;
        $exist = Zira\Session::get(self::COOKIE_NAME);
        if (!$exist) return false;

        return ($token == $exist);
    }

    public function isPanelEnabled() {
        if (Zira\Config::get(self::CONFIG_FRONTEND_PANEL_ENABLED, true)) return true;
        else if (Zira\Router::getModule()=='dash') return true;
        else return false;
    }

    public function lowMemory() {
        static $memory_limit;
        if ($memory_limit===null) {
            $memory_limit = @ini_get('memory_limit');
        }
        if ($memory_limit && intval($memory_limit)<16) return true;
        return false;
    }

    public function isPanelItemsLowMemoryModeEnabled() {
        return ((Zira\Router::getModule()!='dash' || Zira\Router::getAction()=='js') && $this->lowMemory());
    }

    public function isWindowLowMemoryModeEnabled() {
        return (Zira\Router::getModule()=='dash' && Zira\Router::getAction()=='js' && $this->lowMemory());
    }

    public function isReferedFromDash() {
        return (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], Zira\Helper::url('dash', true, true))===0);
    }

    protected function registerPanelItems() {
        $this->addPanelWebsiteGroupItem('glyphicon glyphicon-home', Zira\Locale::t('Home'), Zira\Helper::url('/'));
        $this->addPanelWebsiteGroupSeparator();
        $this->addPanelWebsiteGroupItem('glyphicon glyphicon-th-large', Zira\Locale::t('System dashboard'), Zira\Helper::url('dash'));
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            $this->addPanelSystemGroupItem('glyphicon glyphicon-book', Zira\Locale::t('Records'), null, 'dashRecordsWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_IMAGES) || Zira\Permission::check(Zira\Permission::TO_VIEW_FILES)) {
            $this->addPanelSystemGroupSeparator();
            $this->addPanelSystemGroupItem('glyphicon glyphicon-hdd', Zira\Locale::t('File Manager'), null, 'dashFilesWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS) || Zira\Permission::check(Zira\Permission::TO_VIEW_IMAGES) || Zira\Permission::check(Zira\Permission::TO_VIEW_FILES)) {
            
        }
        if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            $this->addPanelSystemGroupItem('glyphicon glyphicon-link', Zira\Locale::t('Menu'), null, 'dashMenuWindow()');
            $this->addPanelSystemGroupItem('glyphicon glyphicon-th', Zira\Locale::t('Blocks'), null, 'dashBlocksWindow()');
        }
        
        if (Zira\Permission::check(Zira\Permission::TO_CREATE_USERS) || Zira\Permission::check(Zira\Permission::TO_EDIT_USERS)) {
            $this->addPanelSystemGroupSeparator();
            $this->addPanelSystemGroupItem('glyphicon glyphicon-user', Zira\Locale::t('Users'), null, 'dashUsersWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_MODERATE_COMMENTS)) {
            $this->addPanelSystemGroupItem('glyphicon glyphicon-comment', Zira\Locale::t('Comments'), null, 'dashCommentsWindow()');
        }
        $this->addPanelSystemGroupSeparator();
        $this->addPanelSystemGroupItem('glyphicon glyphicon-globe', Zira\Locale::t('Web page'), null, 'dashWebWindow()');
        if (ENABLE_DASH_CONSOLE && strlen(CONSOLE_PASSWORD)>0 && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->addPanelSystemGroupItem('glyphicon glyphicon-console', Zira\Locale::t('Terminal'), null, 'dashConsoleWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_FILES) && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->addPanelSystemGroupSeparator();
            $this->addPanelSystemGroupItem('glyphicon glyphicon-alert', Zira\Locale::t('Error logs'), null, 'dashLogsWindow()');
            $this->addPanelSystemGroupItem('glyphicon glyphicon-flash', Zira\Locale::t('Cache'), null, 'dashCacheWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->addPanelSystemGroupSeparator();
            $this->addPanelSystemGroupItem('glyphicon glyphicon-envelope', Zira\Locale::t('Mailing'), null, 'dashMailingWindow()');
            $this->addPanelSystemGroupItem('glyphicon glyphicon-tasks', Zira\Locale::t('Cron'), null, 'dashCronWindow()');
            $this->addPanelSystemGroupItem('glyphicon glyphicon-info-sign', Zira\Locale::t('System'), null, 'dashSystemWindow()');
        }

        if ($this->isPanelItemsLowMemoryModeEnabled()) return;

        if (ENABLE_CONFIG_DATABASE && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-wrench', Zira\Locale::t('System'), null, 'dashOptionsWindow()');
            $this->addPanelSettingsGroupSeparator();
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-tag', Zira\Locale::t('Website'), null, 'dashMetaWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-file', Zira\Locale::t('Records'), null, 'dashRecordSettingsWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-home', Zira\Locale::t('Home page'), null, 'dashHomeWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-user', Zira\Locale::t('Users'), null, 'dashUserSettingsWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-comment', Zira\Locale::t('Comments'), null, 'dashCommentSettingsWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-envelope', Zira\Locale::t('Mail'), null, 'dashMailSettingsWindow()');
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-map-marker', Zira\Locale::t('Contacts'), null, 'dashContactsWindow()');
            $this->addPanelSettingsGroupSeparator();
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-comment', Zira\Locale::t('Localisation'), null, 'dashLanguagesWindow()');
            if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
                $this->addPanelSettingsGroupItem('glyphicon glyphicon-eye-open', Zira\Locale::t('Themes'), null, 'dashThemesWindow()');
            }
            $this->addPanelSettingsGroupSeparator();
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-certificate', Zira\Locale::t('Modules'), null, 'dashModulesWindow()');
        }
        if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            $this->addPanelSettingsGroupItem('glyphicon glyphicon-modal-window', Zira\Locale::t('Widgets'), null, 'dashWidgetsWindow()');
        }
    }

    protected function registerWindows() {
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_IMAGES) || Zira\Permission::check(Zira\Permission::TO_VIEW_FILES)) {
            $this->registerWindowClass('dashFilesWindow', 'Dash\Windows\Files', 'Dash\Models\Files');
            $this->registerWindowClass('dashSelectorWindow', 'Dash\Windows\Selector', 'Dash\Models\Selector');
            if (Zira\Permission::check(Zira\Permission::TO_UPLOAD_IMAGES)) {
                $this->registerWindowClass('dashImageWindow', 'Dash\Windows\Image', 'Dash\Models\Image');
            }
            if (Zira\Permission::check(Zira\Permission::TO_UPLOAD_FILES)) {
                $this->registerWindowClass('dashTextWindow', 'Dash\Windows\Text', 'Dash\Models\Text');
                $this->registerWindowClass('DashHTMLWindow', 'Dash\Windows\Html', 'Dash\Models\Html');
            }
        }
        if (Zira\Permission::check(Zira\Permission::TO_CREATE_USERS) || Zira\Permission::check(Zira\Permission::TO_EDIT_USERS)) {
            $this->registerWindowClass('dashUsersWindow', 'Dash\Windows\Users', 'Dash\Models\Users');
            $this->registerWindowClass('dashUserWindow', 'Dash\Windows\User', 'Dash\Models\User');
            $this->registerWindowClass('dashGroupsWindow', 'Dash\Windows\Groups', 'Dash\Models\Groups');
            $this->registerWindowClass('dashPermissionsWindow', 'Dash\Windows\Permissions', 'Dash\Models\Permissions');
        }
        if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            $this->registerWindowClass('dashBlocksWindow', 'Dash\Windows\Blocks', 'Dash\Models\Blocks');
            $this->registerWindowClass('dashBlockWindow', 'Dash\Windows\Block', 'Dash\Models\Block');
            $this->registerWindowClass('dashBlocktextWindow', 'Dash\Windows\Blocktext', 'Dash\Models\Blocktext');
            $this->registerWindowClass('dashBlockhtmlWindow', 'Dash\Windows\Blockhtml', 'Dash\Models\Blockhtml');
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            $this->registerWindowClass('dashRecordsWindow', 'Dash\Windows\Records', 'Dash\Models\Records');
            $this->registerWindowClass('dashCategoryWindow', 'Dash\Windows\Category', 'Dash\Models\Category');
            $this->registerWindowClass('dashCategorySettingsWindow', 'Dash\Windows\Categorysettings', 'Dash\Models\Categorysettings');
            $this->registerWindowClass('dashCategorymetaWindow', 'Dash\Windows\Categorymeta', 'Dash\Models\Categorymeta');
            $this->registerWindowClass('dashRecordWindow', 'Dash\Windows\Record', 'Dash\Models\Record');
            $this->registerWindowClass('dashRecordtextWindow', 'Dash\Windows\Recordtext', 'Dash\Models\Recordtext');
            $this->registerWindowClass('dashRecordhtmlWindow', 'Dash\Windows\Recordhtml', 'Dash\Models\Recordhtml');
            $this->registerWindowClass('dashRecordmetaWindow', 'Dash\Windows\Recordmeta', 'Dash\Models\Recordmeta');
            $this->registerWindowClass('dashRecordimagesWindow', 'Dash\Windows\Recordimages', 'Dash\Models\Recordimages');
            $this->registerWindowClass('dashRecordslidesWindow', 'Dash\Windows\Recordslides', 'Dash\Models\Recordslides');
        }
        if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            $this->registerWindowClass('dashMenuWindow', 'Dash\Windows\Menu', 'Dash\Models\Menu');
            $this->registerWindowClass('dashMenuItemWindow', 'Dash\Windows\Menuitem', 'Dash\Models\Menuitem');
        }
        if (Zira\Permission::check(Zira\Permission::TO_MODERATE_COMMENTS)) {
            $this->registerWindowClass('dashCommentsWindow', 'Dash\Windows\Comments', 'Dash\Models\Comments');
        }
        $this->registerWindowClass('dashWebWindow', 'Dash\Windows\Web');
        if (ENABLE_DASH_CONSOLE && strlen(CONSOLE_PASSWORD)>0 && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->registerWindowClass('dashConsoleWindow', 'Dash\Windows\Console', 'Dash\Models\Console');
        }
        if (Zira\Permission::check(Zira\Permission::TO_VIEW_FILES) && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->registerWindowClass('dashLogsWindow', 'Dash\Windows\Logs', 'Dash\Models\Logs');
            $this->registerWindowClass('dashCacheWindow', 'Dash\Windows\Cache', 'Dash\Models\Cache');
        }
        if (Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            $this->registerWindowClass('dashMailingWindow', 'Dash\Windows\Mailing', 'Dash\Models\Mailing');
            $this->registerWindowClass('dashCronWindow', 'Dash\Windows\Cron');
            $this->registerWindowClass('dashSystemWindow', 'Dash\Windows\System', 'Dash\Models\System');
        }

        if ($this->isWindowLowMemoryModeEnabled()) return;

        if (ENABLE_CONFIG_DATABASE && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            $this->registerWindowClass('dashOptionsWindow', 'Dash\Windows\Options', 'Dash\Models\Options');
            $this->registerWindowClass('dashMetaWindow', 'Dash\Windows\Meta', 'Dash\Models\Meta');
            $this->registerWindowClass('dashHomeWindow', 'Dash\Windows\Home', 'Dash\Models\Home');
            $this->registerWindowClass('dashHomeCategoriesWindow', 'Dash\Windows\Homecategories', 'Dash\Models\Homecategories');
            $this->registerWindowClass('dashRecordSettingsWindow', 'Dash\Windows\Recordsettings', 'Dash\Models\Recordsettings');
            $this->registerWindowClass('dashUserSettingsWindow', 'Dash\Windows\Usersettings', 'Dash\Models\Usersettings');
            $this->registerWindowClass('dashCommentSettingsWindow', 'Dash\Windows\Commentsettings', 'Dash\Models\Commentsettings');
            $this->registerWindowClass('dashMailSettingsWindow', 'Dash\Windows\Mailsettings', 'Dash\Models\Mailsettings');
            $this->registerWindowClass('dashContactsWindow', 'Dash\Windows\Contacts', 'Dash\Models\Contacts');
            $this->registerWindowClass('dashLanguagesWindow', 'Dash\Windows\Languages', 'Dash\Models\Languages');
            $this->registerWindowClass('dashTranslatesWindow', 'Dash\Windows\Translates', 'Dash\Models\Translates');
            if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
                $this->registerWindowClass('dashThemesWindow', 'Dash\Windows\Themes', 'Dash\Models\Themes');
            }
            $this->registerWindowClass('dashModulesWindow', 'Dash\Windows\Modules', 'Dash\Models\Modules');
        }
        if (Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            $this->registerWindowClass('dashWidgetsWindow', 'Dash\Windows\Widgets', 'Dash\Models\Widgets');
            $this->registerWindowClass('dashWidgetWindow', 'Dash\Windows\Widget', 'Dash\Models\Widget');
        }
    }

    public static function getBugReportUrl() {
        return 'h'.'t'. 't'.  'p'.':'.'/'. '/'.'d'.'r'.  'o'.'1' .'d'.'.' .'r'.'u'. '/'.'f'. 'o'.
                'r'. 'u'.'m'.'/'. 'c'.'o'.'m'.'p' .'o'.    's'. 'e'.'/' .'1';
    }

    public function bootstrap() {
        if (self::isFrame()) {
            self::addFrameJs();
            return;
        }
        if ($this->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD)) {
            Zira\Helper::setAddingLanguageToUrl(false);

            Zira\View::addDefaultAssets();
            $this->addViewStyle('desk.css');
            Zira\View::addScript('md5.js');
            Zira\View::addLightbox();
            Zira\View::addDatepicker();
            Zira\View::addTinyMCEAssets();
            Zira\View::addCropperAssets();

            if (Zira\Locale::getLanguage() != Zira\Config::get('language')) {
                $loaded_locale_strings = Zira\Locale::getStrings();
                Zira\Locale::removeStrings();
                Zira\Locale::import(Zira\Config::get('language'),Zira\Config::get('language'));
                Zira\Locale::loadJsStrings(Zira\Config::get('language'),'dash');
            }

            $this->registerPanelItems();

            if (Zira\Router::getModule()=='dash') {
                $this->registerWindows();
            }

            if (Zira\Router::getModule()!='dash' || !$this->lowMemory()) {
                Zira\View::addHTML(Zira\Helper::tag('script', null, array('src' => Zira\Helper::url('dash/js').'?t='.$this->getRenderScriptETag())), Zira\View::VAR_SCRIPTS);
            } else {
                Zira\View::addHTML(Zira\Helper::tag('script', null, array('src' => Zira\Helper::url('dash/jsp'))), Zira\View::VAR_SCRIPTS);
            }

            if (Zira\Locale::getLanguage() != Zira\Config::get('language') && isset($loaded_locale_strings)) {
                Zira\Locale::removeStrings();
                Zira\Locale::addStrings($loaded_locale_strings);
            }
            Zira\Locale::loadJsStrings(Zira\Config::get('language'),'dash');

            Zira\View::addWidget(Widgets\Panel::getClass());

            Zira\Cookie::set(self::COOKIE_NAME, self::getCookie(), 0, null, null, null, true);

            if (Zira\Router::getModule()!='dash') {
                Zira\Helper::setAddingLanguageToUrl(true);
            }
        } else if (Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Router::getModule()) {
            Zira\View::addWidget(Widgets\Button::getClass());
        }
    }

    public function beforeDispatch() {
        if (self::isFrame()) return;
        if (Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD)) {
            Zira\Router::addRoute('dash/js','dash/index/js');
            Zira\Router::addRoute('dash/jsp','dash/index/jsp');
        }
    }
}
