<?php
/**
 * Zira project.
 * oauth.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Oauth;

use Zira;

class Oauth {
    protected static $_facebook_js_sdk_added = false;
    protected static $_facebook_js_view_added = false;
    protected static $_vkontakte_js_view_added = false;
    protected static $_vkontakte_js_open_api_added = false;

    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function bootstrap() {
        if (Zira\Config::get('oauth_fb_on') && Zira\Config::get('oauth_fb_app_id') && Zira\Config::get('oauth_fb_app_secret')) {
            Zira\Hook::register(Zira\Forms\User\Login::HOOK_NAME, array(get_class(), 'fb_login_form_hook'));
        }
        if (Zira\Config::get('oauth_vk_on') && Zira\Config::get('oauth_vk_app_id') && Zira\Config::get('oauth_vk_app_secret')) {
            Zira\Hook::register(Zira\Forms\User\Login::HOOK_NAME, array(get_class(), 'vk_login_form_hook'));
        }

        if (ENABLE_CONFIG_DATABASE && \Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_OPTIONS)) {
            \Dash\Dash::loadDashLanguage();
            \Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-log-in', Zira\Locale::tm('Social networks', 'oauth', null, \Dash\Dash::getDashLanguage()), null, 'oauthSettingsWindow()');
            \Dash\Dash::getInstance()->registerModuleWindowClass('oauthSettingsWindow', 'Oauth\Windows\Settings', 'Oauth\Models\Settings');
            \Dash\Dash::unloadDashLanguage();
        }
    }

    public static function getFacebookJsSdkUrl() {
        if (Zira\Locale::getLanguage()=='ru')
            return Models\Oauth::FACEBOOK_JS_SDK_RU;
        else
            return Models\Oauth::FACEBOOK_JS_SDK;
    }

    public static function getFacebookLoginBtn() {
        //return Zira\Helper::tag('fb:login-button', null, array('scope'=>'public_profile,email','onlogin'=>'oauth_fb_login();'));
        return Zira\Helper::tag('a', null, array('href'=>'javascript:void(0)','onclick'=>'oauth_fb_btn();','class'=>'oauth-btn social-btn fb','title'=>Zira\Locale::tm('Login with: %s', 'oauth', Zira\Locale::t('Facebook'))));
    }

    public static function addFacebookJsSdk($app_id) {
        if (self::$_facebook_js_sdk_added) return;
        $js = Zira\Helper::tag_open('script', array('type'=>'text/javascript'))."\r\n";
        $js .='window.fbAsyncInit = function() {'."\r\n".
                "\t".'FB.init({'."\r\n".
                    "\t\t".'appId      : \''.Zira\Helper::html($app_id).'\','."\r\n".
                    "\t\t".'cookie     : true,  // enable cookies'."\r\n".
                    "\t\t".'xfbml      : true,  // parse social plugins on this page'."\r\n".
                    "\t\t".'version    : \'v2.2\' // use version 2.2'."\r\n".
                "\t".'});'."\r\n".
                "\t".'if (typeof(oauth_fb_btn)!="undefined") oauth_fb_btn.activated = true;'."\r\n".
            '};'."\r\n";
        $js .='(function(d, s, id) {'."\r\n".
                "\t".'var js, fjs = d.getElementsByTagName(s)[0];'."\r\n".
                "\t".'if (d.getElementById(id)) return;'."\r\n".
                "\t".'js = d.createElement(s); js.id = id;'."\r\n".
                "\t".'js.src = "'.self::getFacebookJsSdkUrl().'";'."\r\n".
                "\t".'fjs.parentNode.insertBefore(js, fjs);'."\r\n".
                '}(document, \'script\', \'facebook-jssdk\'));'."\r\n";
        $js .= Zira\Helper::tag_close('script')."\r\n";
        //Zira\View::addHTML($js, Zira\View::VAR_BODY_BOTTOM);
        Zira\View::addBodyBottomScript($js);
        self::$_facebook_js_sdk_added = true;
    }

    public static function addFacebookJSView() {
        if (self::$_facebook_js_view_added) return;
        self::addFacebookJsSdk(Zira\Config::get('oauth_fb_app_id'));
        Zira\View::addPlaceholderView(Zira\View::VAR_BODY_BOTTOM, array(
            'app_id' => Zira\Config::get('oauth_fb_app_id'),
            'app_secret' => Zira\Config::get('oauth_fb_app_secret')
        ), 'oauth/fb-login');
        self::$_facebook_js_view_added = true;
    }

    public static function fb_login_form_hook() {
        self::addFacebookJSView();
        return self::getFacebookLoginBtn();
    }

    public static function includeFacebookSdk() {
        require_once(ROOT_DIR . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . Models\Oauth::FACEBOOK_SDK_FOLDER . DIRECTORY_SEPARATOR . 'autoload.php');
    }

    public static function getVkontakteAuthUrl() {
        return Models\Oauth::VKONTAKTE_AUTH_URL . '?client_id='.Zira\Config::get('oauth_vk_app_id').'&redirect_uri='.Zira\Helper::url('oauth/login/vkresponse', true, true).'&display=popup&scope=email&response_type=code&v=5.50';
    }

    public static function getVkontakteAccessTokenUrl($code) {
        return Models\Oauth::VKONTAKTE_ACCESS_TOKEN_URL . '?client_id='.Zira\Config::get('oauth_vk_app_id').'&client_secret='.Zira\Config::get('oauth_vk_app_secret').'&redirect_uri='.Zira\Helper::url('oauth/login/vkresponse', true, true).'&code='.$code;
    }

    public static function getVkontakteUserApiUrl($access_token, $user_id) {
        return Models\Oauth::VKONTAKTE_USER_API_URL . '?user_id='.$user_id.'&access_token='.$access_token.'&v=5.50';
    }

    public static function getVkontakteLoginBtn() {
        return Zira\Helper::tag('a', null, array('href'=>'javascript:void(0)','onclick'=>'oauth_vk_login();','class'=>'oauth-btn social-btn vk','title'=>Zira\Locale::tm('Login with: %s', 'oauth', Zira\Locale::t('Vkontakte'))));
    }

    public static function addVkontakteJSView() {
        if (self::$_vkontakte_js_view_added) return;
        Zira\View::addPlaceholderView(Zira\View::VAR_BODY_BOTTOM, array(
            'app_id' => Zira\Config::get('oauth_vk_app_id'),
            'app_secret' => Zira\Config::get('oauth_vk_app_secret')
        ), 'oauth/vk-login');
        self::$_vkontakte_js_view_added = true;
    }

    public static function addVkontakteOpenApi($app_id) {
        if (self::$_vkontakte_js_open_api_added) return;
        $js = Zira\Helper::tag('div', null, array('id'=>'vk_api_transport'))."\r\n";
        $js .= Zira\Helper::tag_open('script', array('type'=>'text/javascript'))."\r\n";
        $js .= 'if (typeof(vk_open_api_init_callbacks)=="undefined") vk_open_api_init_callbacks = [];'."\r\n";
        $js .= 'window.vkAsyncInit = function() {'."\r\n".
                "\t".'VK.init({apiId: '.$app_id.', onlyWidgets: true});'."\r\n".
                "\t".'for(var i=0; i<vk_open_api_init_callbacks.length; i++){'."\r\n".
                "\t".'vk_open_api_init_callbacks[i].call();'."\r\n".
                "\t".'}'."\r\n".
        '};'."\r\n".
        'setTimeout(function() {'."\r\n".
            "\t".'var el = document.createElement("script");'."\r\n".
            "\t".'el.type = "text/javascript";'."\r\n".
            "\t".'el.src = "//vk.com/js/api/openapi.js";'."\r\n".
            "\t".'el.async = true;'."\r\n".
            "\t".'document.getElementById("vk_api_transport").appendChild(el);'."\r\n".
        '}, 0);'."\r\n";
        $js .= Zira\Helper::tag_close('script')."\r\n";
        //Zira\View::addHTML($js, Zira\View::VAR_BODY_BOTTOM);
        Zira\View::addBodyBottomScript($js);
        self::$_vkontakte_js_open_api_added = true;
    }

    public static function vk_login_form_hook() {
        self::addVkontakteJSView();
        return self::getVkontakteLoginBtn();
    }

    public static function getRedirectUrl() {
        $redirect = Zira\Request::get('redirect');
        if (!empty($redirect) && strpos($redirect,'//')===false && strpos($redirect, '.')===false) {
            return $redirect;
        } else {
            $redirect_url = Zira\Page::getRedirectUrl();
            if (!$redirect_url) $redirect_url = Zira\Page::getRecordUrl();
            if ($redirect_url && $redirect_url==Zira\Config::get('home_record_name')) $redirect_url = null;
            if (!$redirect_url && Zira\Category::current()) $redirect_url = Zira\Category::current()->name;
            return $redirect_url ? $redirect_url : '';
        }
    }
}