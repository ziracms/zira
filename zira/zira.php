<?php
/**
 * Zira project
 * zira.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

use Dash\Dash;

class Zira {
    const VERSION = '1.4.1';
    private static $_instance;
    private static $_vendor_autoload_enabled = false;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function bootstrap() {
        Log::init();
        Session::start();
        Db\Loader::initialize();
        Db\Db::open();
        Config::load();
        Datetime::init();
        User::load();

        self::beforeDispatch();
        Router::dispatch();
        if (Router::getModule() == UPLOADS_DIR && 
            Router::getController() == THUMBS_DIR && 
            Router::getAction() == CUSTOM_THUMBS_ACTION
        ) {
            $this->process();
            return;
        }
        if (Router::getModule() == UPLOADS_DIR) {
            return; // deleted image ?
        }

        Dash::setDashLanguage(Config::get('dash_language', Config::get('language')));
        if (Router::getModule()!='dash') {
            Locale::init();
            if (!Router::getLanguage() || !Locale::load(Router::getLanguage())) {
                $language = Config::get('language') ? Config::get('language') : DEFAULT_LANGUAGE;
                Locale::load($language);
            }
        } else {
            Locale::load(Dash::getDashLanguage());
        }
        Locale::load(null,Router::getModule());
        if (Locale::getLanguage() && Config::get('db_translates')) {
            Locale::loadDBStrings();
        }

        Page::addBreadcrumb('/', Locale::t('Home'));
        if (Router::getModule()!='dash') {
            $request = Router::getRequest();
            if (!empty($request) && !Request::isAjax()) {
                Category::load($request);
            }
        }

        $theme = Config::get('theme') ? Config::get('theme') : DEFAULT_THEME;
        View::setTheme($theme);
        Assets::init();

        self::bootstrapModules();
        if (Router::getModule()!='dash') {
            self::registerDbWidgets();
        } else {
            Widgets::addDefaultDbWidgets();
        }

        Session::remove(Response::SESSION_REDIRECT);
        Locale::remember();

        if (self::isOnline()) {
            $this->process();
        } else {
            Config::set('user_signup_allow', false);
            if ((Router::getModule() == 'zira' && Router::getController() == 'index' && Router::getAction() == 'captcha') ||
                (Router::getModule() == 'zira' && Router::getController() == 'user') ||
                Router::getModule() == 'oauth'
            ) {
                $this->process();
            } else {
                View::setRenderBreadcrumbs(false);
                View::setRenderDbWidgets(false);
                View::render(array(), 'offline', View::LAYOUT_ALL_SIDEBARS);
            }
        }
    }

    public static function enableVendorAutoload() {
        if (self::$_vendor_autoload_enabled) return;
        self::$_vendor_autoload_enabled = true;
        require_once ROOT_DIR . DIRECTORY_SEPARATOR . VENDOR_DIR . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    public static function isVendorAutoloadEnabled() {
        return self::$_vendor_autoload_enabled;
    }
    
    public static function isOnline() {
        return (!Config::get('site_offline') || 
                Permission::check(Permission::TO_ACCESS_DASHBOARD) ||
                Request::isAjax());
    }
    
    protected function registerDbWidgets() {
        if (Request::isAjax()) return;
        $category_id = null;
        if (!Router::getRequest()) {
            $category_id = Category::ROOT_CATEGORY_ID;
        } else if (Category::current()) {
            $chain = Category::chain();
            $category_id = array();
            foreach($chain as $row) {
                $category_id[]=$row->id;
            }
        }
        Widgets::load($category_id);
    }

    public function beforeDispatch() {
        $this->registerRoutes();

        Dash::getInstance()->beforeDispatch();

        foreach(Config::get('modules') as $module) {
            if ($module == 'zira' || $module == 'dash') continue;
            $class = '\\'.ucfirst($module).'\\'.ucfirst($module);
            try {
                if (method_exists($class, 'getInstance')) {
                    $obj = call_user_func($class . '::getInstance');
                } else {
                    $obj = new $class;
                }
            } catch(\Exception $e) {
                continue;
            }
            if (method_exists($obj,'beforeDispatch')) {
                call_user_func(array($obj,'beforeDispatch'));
            }
        }
    }

    protected function registerRoutes() {
        Router::addRoute('captcha','zira/index/captcha');
        Router::addRoute('cron','zira/cron/index');
        Router::addRoute('forbidden','zira/index/forbidden');
        Router::addRoute('notfound','zira/index/notfound');
        Router::addRoute('user','zira/user');
        Router::addRoute('user/*','zira/user/$2');
        Router::addRoute('poll','zira/poll/index');
        Router::addRoute('records','zira/records/index');
        Router::addRoute('comment','zira/comments/comment');
        Router::addRoute('comments','zira/comments/index');
        Router::addRoute('search','zira/search/index');
        Router::addRoute('tags','zira/search/tags');
        Router::addRoute('sitemap','zira/index/map');
        Router::addRoute('rss','zira/xml/rss');
        Router::addRoute('contact','zira/contact/index');
        Router::addRoute('file','zira/index/file');
    }

    public function bootstrapModules() {
        Dash::getInstance()->bootstrap();

        foreach(Config::get('modules') as $module) {
            if ($module == 'zira' || $module == 'dash') continue;
            $class = '\\'.ucfirst($module).'\\'.ucfirst($module);
            try {
                if (method_exists($class, 'getInstance')) {
                    $obj = call_user_func($class . '::getInstance');
                } else {
                    $obj = new $class;
                }
            } catch(\Exception $e) {
                continue;
            }
            if (method_exists($obj,'bootstrap')) {
                call_user_func(array($obj,'bootstrap'));
            }
        }
    }

    public function process() {
        $controller_class = '\\'.ucfirst(Router::getModule()).
            '\\'.ucfirst(CONTROLLERS_DIR).
            '\\'.ucfirst(Router::getController());

        try {
            if (!class_exists($controller_class)) throw new \Exception('Controller class not found');
            $controller_obj = new $controller_class;
            if (!($controller_obj instanceof Controller)) throw new \Exception('Invalid controller class');
            if (!method_exists($controller_obj, Router::getAction())) throw new \Exception('Action not found');
            $param = Router::getParam();
            // only numeric params allowed
            if ((count(explode('/',Router::getRequest()))!=1 || Router::getModule()!=DEFAULT_MODULE || Router::getController()!=DEFAULT_CONTROLLER || Router::getAction()!=DEFAULT_ACTION) &&
                (Router::getModule()!=UPLOADS_DIR || Router::getController()!=THUMBS_DIR || Router::getAction()!=CUSTOM_THUMBS_ACTION) && 
                !empty($param) && (!is_numeric($param) || intval($param)<=0)
            ) {
                throw new \Exception('Bad request');
            }
            // checking if param is accepted
            if (strlen($param)>0 && (Router::getModule()!=DEFAULT_MODULE || Router::getController()!=DEFAULT_CONTROLLER || Router::getAction()!=DEFAULT_ACTION)) {
                $reflectionMethod = new \ReflectionMethod($controller_obj, Router::getAction());
                if ($reflectionMethod->getNumberOfParameters() == 0) {
                    throw new \Exception('Bad request');
                }
            }
            // catch and process pages
            if (Router::getRequest() && Router::getModule()==DEFAULT_MODULE && Router::getController()==DEFAULT_CONTROLLER && Router::getAction()==DEFAULT_ACTION) {
                throw new \Exception('Bad request');
            } else {
                $param = intval($param);
            }
        } catch (\Exception $e) {
            $controller_obj = null;
            $param = null;
            if (Router::getModule()==UPLOADS_DIR && 
                Router::getController()==THUMBS_DIR && 
                Router::getAction()==CUSTOM_THUMBS_ACTION
            ) {
                $controller_obj = new Controllers\Index();
                Router::setModule(DEFAULT_MODULE);
                Router::setController(DEFAULT_CONTROLLER);
                Router::setAction(CUSTOM_THUMBS_ACTION);
                $param = Request::uri();
            } else if (Category::current()) {
                $controller_obj = new Controllers\Index();
                Router::setAction('page');
                Router::setController(DEFAULT_CONTROLLER);
                Router::setModule(DEFAULT_MODULE);
                $param = Category::param();
            } else if (count(explode('/',Router::getRequest()))==1) {
                Router::setModule(DEFAULT_MODULE);
                $param = Router::getRequest();
                if ($param == 'sitemap.xml') {
                    $controller_obj = new Controllers\Xml();
                    Router::setController('xml');
                    Router::setAction('sitemap');
                } else {
                    $controller_obj = new Controllers\Index();
                    Router::setController(DEFAULT_CONTROLLER);
                    Router::setAction('page');
                }
            } else {
                Response::notFound();
            }
        }

        if (
            Router::getModule()!='zira' && Router::getModule()!='dash'
            && !in_array(Router::getModule(), Config::get('modules')
        )) {
            Response::notFound();
        }

        call_user_func(array($controller_obj, '_before'));
        call_user_func_array(array($controller_obj, Router::getAction()), array($param));
        call_user_func(array($controller_obj, '_after'));
    }

    public static function getModuleCronTasks($module) {
        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'cron';
        if (!file_exists($dir) || !is_dir($dir)) return array();
        $objects = array();
        $d = opendir($dir);
        while(($f=readdir($d))!==false) {
            if ($f=='.' || $f=='..' || is_dir($dir. DIRECTORY_SEPARATOR . $f)) continue;
            if (!preg_match('/^([a-zA-Z0-9]+)\.php$/', $f, $matches)) continue;
            $class = '\\'.ucfirst($module).'\\Cron\\'.ucfirst($matches[1]);
            try {
                if (class_exists($class)) {
                    $obj = new $class;
                    if ($obj instanceof Cron) {
                        $objects []= $obj;
                    } else {
                        unset($obj);
                    }
                }
            } catch(\Exception $e) {
                Log::exception($e);
            }
        }
        closedir($d);
        return $objects;
    }

    public function exception(\Exception $e) {
        if (defined('DEBUG') && DEBUG) throw $e;
        else Log::exception($e);;
    }

    public function shutdown() {
        Session::close();
        Db\Db::close();
    }

    public static function randomSecureString($bytes) {
        return bin2hex(openssl_random_pseudo_bytes($bytes));
    }
}