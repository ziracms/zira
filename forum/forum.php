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
    const PERMISSION_MODERATE = 'Moderate forum';

    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function onActivate() {
        Zira\Assets::registerCSSAsset('forum/forum.css');
        Zira\Assets::registerJSAsset('forum/forum.js');
    }

    public function onDeactivate() {
        Zira\Assets::unregisterCSSAsset('forum/forum.css');
        Zira\Assets::unregisterJSAsset('forum/forum.js');
    }

    public function beforeDispatch() {
        Zira\Router::addRoute(self::ROUTE,'forum/index/index');
        Zira\Router::addRoute(self::ROUTE.'/group','forum/index/group');
        Zira\Router::addRoute(self::ROUTE.'/threads','forum/index/threads');
        Zira\Router::addRoute(self::ROUTE.'/thread','forum/index/thread');
        Zira\Router::addRoute(self::ROUTE.'/compose','forum/index/compose');
        Zira\Router::addRoute(self::ROUTE.'/poll','forum/index/poll');
        Zira\Router::addRoute(self::ROUTE.'/user','forum/index/user');
        Zira\Router::addRoute(self::ROUTE.'/search','forum/index/search');

        Zira\Assets::registerCSSAsset('forum/forum.css');
        Zira\Assets::registerJSAsset('forum/forum.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('forum/forum.css');
        Zira\View::addScript('forum/forum.js');
        Zira\View::addParser(); // required for widget

        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && (Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS) || Zira\Permission::check(self::PERMISSION_MODERATE))) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-comment', Zira\Locale::tm('Forum', 'forum', null, Dash\Dash::getDashLanguage()), null, 'forumsWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumsWindow', 'Forum\Windows\Forums', 'Forum\Models\Forums');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumCategoriesWindow', 'Forum\Windows\Categories', 'Forum\Models\Categories');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumCategoryWindow', 'Forum\Windows\Category', 'Forum\Models\Categories');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumWindow', 'Forum\Windows\Forum', 'Forum\Models\Forums');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumTopicsWindow', 'Forum\Windows\Topics', 'Forum\Models\Topics');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumTopicWindow', 'Forum\Windows\Topic', 'Forum\Models\Topics');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumMessagesWindow', 'Forum\Windows\Messages', 'Forum\Models\Messages');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumMessageWindow', 'Forum\Windows\Message', 'Forum\Models\Messages');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumFilesWindow', 'Forum\Windows\Files', 'Forum\Models\Files');
            Dash\Dash::getInstance()->registerModuleWindowClass('forumSettingsWindow', 'Forum\Windows\Settings', 'Forum\Models\Settings');
            Dash\Dash::unloadDashLanguage();
        }

        if (Zira\User::isAuthorized()) {
            Zira\Hook::register(Zira\User::PROFILE_LINKS_HOOK, array(get_class(), 'profileLinksHook'));
            if (Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD)) {
                Zira\Hook::register(Dash\Dash::NOTIFICATION_HOOK, array(get_class(), 'newMessagesHook'));
            }
        }
        Zira\Hook::register(Zira\User::PROFILE_INFO_HOOK, array(get_class(), 'profileInfoHook'));
    }

    public static function profileLinksHook() {
        return array(
                'url' => self::ROUTE.'/user',
                'icon' => 'glyphicon glyphicon-comment',
                'title' => Zira\Locale::tm('My discussions', 'forum')
            );
    }

    public static function profileInfoHook($user) {
        return array(
                'icon' => 'glyphicon glyphicon-pencil',
                'title' => Zira\Locale::tm('Forum posts', 'forum'),
                'description' => (int)$user->posts
            );
    }

    public static function newMessagesHook() {
        $messages = \Forum\Models\Message::getNewMessagesCount();
        if ($messages == 0) return false;
        return array(
                'message'=>Zira\Locale::tm('%s forum messages was posted', 'forum', $messages),
                'callback'=>Dash\Dash::getInstance()->getWindowJSName(\Forum\Windows\Forums::getClass())
            );
    }
}