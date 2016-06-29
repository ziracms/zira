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

    public function beforeDispatch() {
        Zira\Router::addRoute(self::ROUTE,'forum/index/index');
        Zira\Router::addRoute(self::ROUTE.'/group','forum/index/group');
        Zira\Router::addRoute(self::ROUTE.'/threads','forum/index/threads');
        Zira\Router::addRoute(self::ROUTE.'/thread','forum/index/thread');
        Zira\Router::addRoute(self::ROUTE.'/compose','forum/index/compose');
        Zira\Router::addRoute(self::ROUTE.'/poll','forum/index/poll');

        Zira\Assets::registerCSSAsset('forum/forum.css');
        Zira\Assets::registerJSAsset('forum/forum.js');

        Zira\Router::addAvailableRoute('forum');
        Zira\Router::addAvailableRoute('forum/group');
        Zira\Router::addAvailableRoute('forum/threads');
        Zira\Router::addAvailableRoute('forum/thread');

        if (Zira\Request::uri() == Zira\Helper::url('forum') || strpos(Zira\Request::uri(), Zira\Helper::url('forum').'/')===0) {
            Zira\Category::setAddBreadcrumbs(false);
        }
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('forum/forum.css');
        Zira\View::addScript('forum/forum.js');
        Zira\View::addParser(); // required for widget

        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && (Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS) || Zira\Permission::check(self::PERMISSION_MODERATE))) {
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-comment', Zira\Locale::tm('Forum', 'forum'), null, 'forumsWindow()');
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
        }
    }

    public static function category() {
        if (!Zira\Category::current()) return;

        // checking permission
        if (Zira\Category::current()->access_check && !Zira\Permission::check(Zira\Permission::TO_VIEW_RECORDS)) {
            return;
        }

        $record = Zira\Content\Category::record(Zira\Category::current());

        // adding meta tags
        $title = Zira\Category::current()->title;
        if (Zira\Category::current()->meta_title) $meta_title = Zira\Category::current()->meta_title;
        else $meta_title = Zira\Category::current()->title;
        if (Zira\Category::current()->meta_keywords) $meta_keywords = Zira\Locale::t(Zira\Category::current()->meta_keywords);
        else $meta_keywords = mb_strtolower(Zira\Locale::t(Zira\Category::current()->title), CHARSET);
        if (Zira\Category::current()->meta_description) $meta_description = Zira\Category::current()->meta_description;
        else if (Zira\Category::current()->description) $meta_description = Zira\Category::current()->description;
        else $meta_description = Zira\Locale::t('Category: %s', Zira\Category::current()->title);
        $thumb = null;

        if ($record) {
            $title = $record->title;
            if ($record->meta_title) $meta_title = $record->meta_title;
            else $meta_title = $record->title;
            if ($record->meta_keywords) $meta_keywords = $record->meta_keywords;
            if ($record->meta_description) $meta_description = $record->meta_description;
            else $meta_description = $record->description;
            if ($record->thumb) $thumb = $record->thumb;
        }

//        Zira\Page::addTitle(Zira\Locale::t($meta_title));
//        Zira\Page::setKeywords($meta_keywords);
//        Zira\Page::setDescription(Zira\Locale::t($meta_description));

        $limit = Zira\Config::get('records_limit', 10);
        if (Zira\Category::current()->records_list===null || Zira\Category::current()->records_list) {
            $records = Zira\Page::getRecords(Zira\Category::current(), false, $limit + 1, null, Zira\Config::get('category_childs_list', true), Zira\Category::currentChilds());
        } else {
            $records = array();
        }

        $comments_enabled = Zira\Category::current()->comments_enabled!==null ? Zira\Category::current()->comments_enabled : Zira\Config::get('comments_enabled', 1);
        $rating_enabled = Zira\Category::current()->rating_enabled!==null ? Zira\Category::current()->rating_enabled : Zira\Config::get('rating_enabled', 0);
        $display_author = Zira\Category::current()->display_author!==null ? Zira\Category::current()->display_author : Zira\Config::get('display_author', 0);
        $display_date = Zira\Category::current()->display_date!==null ? Zira\Category::current()->display_date : Zira\Config::get('display_date', 0);

        $data = array(
                Zira\Page::VIEW_PLACEHOLDER_CLASS => 'records',
                Zira\Page::VIEW_PLACEHOLDER_RECORDS => $records,
                Zira\Page::VIEW_PLACEHOLDER_SETTINGS => array(
                    'limit' => $limit,
                    'comments_enabled' => $comments_enabled,
                    'rating_enabled' => $rating_enabled,
                    'display_author' => $display_author,
                    'display_date' => $display_date
                )
        );

        $_data = array(
            //Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t($title)
        );

        if ($record) {
            $_data[Zira\Page::VIEW_PLACEHOLDER_IMAGE] = $record->image;
            $_data[Zira\Page::VIEW_PLACEHOLDER_CONTENT] = $record->content;
            $_data[Zira\Page::VIEW_PLACEHOLDER_CLASS] = 'parse-content';
            Zira\View::addParser();
        } else {
            $_data[Zira\Page::VIEW_PLACEHOLDER_DESCRIPTION] = Zira\Locale::t(Zira\Category::current()->description);
        }

        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $_data, 'page');
        Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT, $data, 'zira/list');
        Zira\View::preloadThemeLoader();
    }
}