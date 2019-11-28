<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push;

use Zira;

class Push {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function onActivate() {
        Zira\Assets::registerCSSAsset('push/push.css');
        Zira\Assets::registerJSAsset('push/push.js');
    }
    
    public function onDeactivate() {
        Zira\Assets::unregisterCSSAsset('push/push.css');
        Zira\Assets::unregisterJSAsset('push/push.js');
    }
    
    public function beforeDispatch() {
        Zira\Assets::registerCSSAsset('push/push.css');
        Zira\Assets::registerJSAsset('push/push.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('push/push.css');
        Zira\View::addScript('push/push.js');

        Zira\View::addJsStrings(array(
            'Unsubscribe from notifications' => Zira\Locale::tm('Unsubscribe from notifications', 'push')
        ));
        
        if (ENABLE_CONFIG_DATABASE && \Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            \Dash\Dash::loadDashLanguage();
            \Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-cloud-upload', Zira\Locale::tm('Push notifications', 'push', null, \Dash\Dash::getDashLanguage()), null, 'pushSettingsWindow()');
            \Dash\Dash::getInstance()->registerModuleWindowClass('pushSettingsWindow', 'Push\Windows\Settings', 'Push\Models\Settings');
            \Dash\Dash::getInstance()->registerModuleWindowClass('pushPushWindow', 'Push\Windows\Push', 'Push\Models\Push');
            \Dash\Dash::unloadDashLanguage();
        }
    }

    public static function getWebPushInstance(array $auth = [], array $defaultOptions = [], ?int $timeout = 30, array $clientOptions = []) {
        if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
        return new \Minishlink\WebPush\WebPush($auth, $defaultOptions, $timeout, $clientOptions);
    }

    public static function createSubscription(array $associativeArray) {
        if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
        \Minishlink\WebPush\Subscription::create($associativeArray);
    }

    public static function createVapidKeys() {
        if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
        return \Minishlink\WebPush\VAPID::createVapidKeys();
    }
}