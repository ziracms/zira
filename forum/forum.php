<?php
/**
 * Zira project.
 * forum.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum;

use Zira;
use Dash;

class Forum {
    const ROUTE = 'forum';
    const VIEW_PLACEHOLDER_LABEL = 'label';

    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        Zira\Router::addRoute(self::ROUTE,'forum/index/index');
        Zira\Router::addRoute(self::ROUTE.'/group','forum/index/group');
        Zira\Router::addRoute(self::ROUTE.'/threads','forum/index/threads');
        Zira\Router::addRoute(self::ROUTE.'/thread','forum/index/thread');
        Zira\Router::addRoute(self::ROUTE.'/compose','forum/index/compose');
        Zira\Router::addRoute(self::ROUTE.'/poll','forum/index/poll');

        Zira\Assets::registerCSSAsset('forum/forum.css');
        Zira\Assets::registerJSAsset('forum/forum.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('forum/forum.css');
        Zira\View::addScript('forum/forum.js');

        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-comment', Zira\Locale::tm('Forum', 'forum'), null, 'forumsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumsWindow', 'Forum\Windows\Forums', 'Forum\Models\Forums');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumCategoriesWindow', 'Forum\Windows\Categories', 'Forum\Models\Categories');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumCategoryWindow', 'Forum\Windows\Category', 'Forum\Models\Categories');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumWindow', 'Forum\Windows\Forum', 'Forum\Models\Forums');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumTopicsWindow', 'Forum\Windows\Topics', 'Forum\Models\Topics');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumTopicWindow', 'Forum\Windows\Topic', 'Forum\Models\Topics');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumMessagesWindow', 'Forum\Windows\Messages', 'Forum\Models\Messages');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumMessageWindow', 'Forum\Windows\Message', 'Forum\Models\Messages');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumSettingsWindow', 'Forum\Windows\Settings', 'Forum\Models\Settings');
        }
    }
}