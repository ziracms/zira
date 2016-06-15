<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote;

use Zira;
use Dash;

class Vote {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function beforeDispatch() {
        Zira\Assets::registerCSSAsset('vote/vote.css');
        Zira\Assets::registerJSAsset('vote/vote.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('vote/vote.css');
        Zira\View::addScript('vote/vote.js');

        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-stats', Zira\Locale::tm('Votes', 'vote'), null, 'votesWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('votesWindow', 'Vote\Windows\Votes', 'Vote\Models\Votes');
            Dash\Dash::getInstance()->registerModuleWindowClass('voteWindow', 'Vote\Windows\Vote', 'Vote\Models\Votes');
            Dash\Dash::getInstance()->registerModuleWindowClass('voteOptionsWindow', 'Vote\Windows\Options', 'Vote\Models\Votes');
        }
    }
}