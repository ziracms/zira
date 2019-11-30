<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push;

use Zira;

class Push {
    const ROUTE = 'subscribe';
    const PHP_MIN_VERSION = '7.1.0';
    
    private static $_instance;
    private static $_web_push_instance;

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
        Zira\Router::addRoute(self::ROUTE,'push/index/index');
        
        Zira\Assets::registerCSSAsset('push/push.css');
        Zira\Assets::registerJSAsset('push/push.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('push/push.css');
        Zira\View::addScript('push/push.js');

        Zira\View::addJsStrings(array(
            'Subscribe to notifications' => Zira\Locale::tm('Subscribe to notifications', 'push'),
            'Unsubscribe from notifications' => Zira\Locale::tm('Unsubscribe from notifications', 'push')
        ));
        
        if (ENABLE_CONFIG_DATABASE && \Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            \Dash\Dash::loadDashLanguage();
            \Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-cloud-upload', Zira\Locale::tm('Push notifications', 'push', null, \Dash\Dash::getDashLanguage()), null, 'pushSettingsWindow()');
            \Dash\Dash::getInstance()->registerModuleWindowClass('pushSettingsWindow', 'Push\Windows\Settings', 'Push\Models\Settings');
            \Dash\Dash::getInstance()->registerModuleWindowClass('pushPushWindow', 'Push\Windows\Push', 'Push\Models\Push');
            \Dash\Dash::unloadDashLanguage();
        }

        if (!Zira\View::isAjax() && Zira\Router::getModule()!='dash') {
            $js_scripts = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
            $is_push_request_on_load_enabled = Zira\Config::get('push_subscribe_onload_on') == 1 ? 1 : 0;
            $push_pub_key = Zira\Config::get('push_pub_key');
            $is_subscription_disabled = Models\Subscription::isSubscriptionDisabled() ? 1 : 0;
            $js_scripts .= 'zira_push_request_onload_on = '.$is_push_request_on_load_enabled.';';
            $js_scripts .= 'zira_push_pub_key = \''.$push_pub_key.'\';';
            $js_scripts .= 'zira_push_service_worker_url = \''.Zira\Helper::baseUrl('sw.js').'\';';
            $js_scripts .= 'zira_push_controller_url = \''.Zira\Helper::url(self::ROUTE).'\';';
            $js_scripts .= 'zira_push_token = \''.Zira\User::getToken().'\';';
            $js_scripts .= 'zira_push_subscription_disabled = '.$is_subscription_disabled.';';
            $js_scripts .= Zira\Helper::tag_close('script');
            Zira\View::addBodyBottomScript($js_scripts);
        }
    }

    public static function getWebPushInstance(array $auth = [], array $defaultOptions = [], $timeout = 30, array $clientOptions = []) {
        if (self::$_web_push_instance === null) {
            if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
            self::$_web_push_instance = new \Minishlink\WebPush\WebPush($auth, $defaultOptions, $timeout, $clientOptions);
        }
        return self::$_web_push_instance;
        
    }

    public static function createSubscription(array $associativeArray) {
        if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
        return \Minishlink\WebPush\Subscription::create($associativeArray);
    }

    public static function createVapidKeys() {
        if (!Zira::isVendorAutoloadEnabled()) Zira::enableVendorAutoload();
        return \Minishlink\WebPush\VAPID::createVapidKeys();
    }

    public static function pushNotification($endpoint, $pubKey, $authToken, $contentEncoding, $title, $body, $image, $url) {
        $push_pub_key = Zira\Config::get('push_pub_key');
        $push_priv_key = Zira\Config::get('push_priv_key');
        if (empty($push_pub_key) || empty($push_priv_key)) return false;
        
        $notification = [
            'subscription' => self::createSubscription([
                'endpoint' => $endpoint,
                'publicKey' => $pubKey,
                'authToken' => $authToken,
                'contentEncoding' => $contentEncoding
            ]),
            'payload' => json_encode(array(
                'title' => Zira\Helper::html($title),
                'body' => Zira\Helper::html($body),
                'icon' => Zira\Helper::baseUrl(Zira\Helper::urlencode($image)),
                'url' => Zira\Helper::url($url)
            ))
        ];
        
        $auth = array(
            'VAPID' => array(
                'subject' => Zira\Helper::url('', true, true),
                'publicKey' => $push_pub_key,
                'privateKey' => $push_priv_key
            )
        );
        
        $webPush = self::getWebPushInstance($auth);
        
        $sent = $webPush->sendNotification(
            $notification['subscription'],
            $notification['payload'],
            true
        );

        if (!$sent->current()->isSuccess()) {
            Zira\Log::write($sent->current()->getReason());
        }
        
        return $sent->current()->isSuccess();
    }
}