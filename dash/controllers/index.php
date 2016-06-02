<?php
/**
 * Zira project.
 * index.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Index extends Dash\Controller {
    public function index() {
        Zira\View::addScript('dash.js');

        $script = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){ ';
        $script .= 'jQuery.post(\''.Zira\Helper::url('dash/index/notifications').'\',{\'token\':\''.Dash\Dash::getToken().'\'},function(response){';
        $script .= 'if (!response || typeof(response.notifications)=="undefined") return;';
        $script .= 'for (var i=0; i<response.notifications.length; i++){';
        $script .= 'dashboard_notification(response.notifications[i].message,response.notifications[i].callback);';
        $script .= '}';
        $script .= '},\'json\');';
        $script .= ' });';
        $script .= Zira\Helper::tag_close('script');
        Zira\View::addHTML($script, Zira\View::VAR_HEAD_BOTTOM);

        Zira\Page::addTitle(Zira\Locale::t('System dashboard'));
        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT => Zira\Helper::tag('div', Zira\Locale::t('Version: %s', Zira::VERSION), array('id'=>'dash-version'))
        ));
    }

    public function load() {
        Zira\View::setAjax(true);
        $response = array();
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('id');
            $className = Zira\Request::post('class');
            $items = Zira\Request::post('items');
            $search = Zira\Request::post('search');
            $page = Zira\Request::post('page');
            $order = Zira\Request::post('order');
            $class = Dash\Dash::getInstance()->getWindowClass($className);
            if ($class && method_exists($class, 'load')) {
                call_user_func(array($class, 'setCallbackStringMode'), true);
                $wnd = new $class();
                $wnd->build();
                $wnd->resetOptions();
                if (property_exists($wnd, 'search')) $wnd->search = (string)$search;
                if (property_exists($wnd, 'page')) $wnd->page = intval($page);
                if (property_exists($wnd, 'order') && in_array($order, array('asc', 'ASC', 'desc', 'DESC'))) $wnd->order = strtolower($order);
                if (property_exists($wnd, 'item') && !empty($items) && is_array($items) && count($items)==1) {
                    $wnd->item = (string)$items[0];
                }
                $return = $wnd->load();
                $response = $wnd->getOptions();
                if (!empty($return) && is_array($return)) {
                    $response=array_merge($response, $return);
                }
                unset($wnd);
            }
        }
        Zira\Page::render($response);
    }

    public function save() {
        Zira\View::setAjax(true);
        $response = array();
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('id');
            $className = Zira\Request::post('class');
            $class = Dash\Dash::getInstance()->getWindowClass($className);
            $model = Dash\Dash::getInstance()->getModelClass($className);
            if ($class && $model && method_exists($model, 'save')) {
                $wnd = new $model(new $class);
                $response = $wnd->save(Zira\Request::post());
                unset($wnd);
            } else if ($class && method_exists($class, 'save')) {
                $wnd = new $class();
                $response = $wnd->save(Zira\Request::post());
                unset($wnd);
            }
        }
        Zira\Page::render($response);
    }

    public function delete() {
        Zira\View::setAjax(true);
        $response = array();
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('id');
            $className = Zira\Request::post('class');
            $items = Zira\Request::post('items');
            $class = Dash\Dash::getInstance()->getWindowClass($className);
            $model = Dash\Dash::getInstance()->getModelClass($className);
            if ($class && $model && method_exists($model, 'delete') && !empty($items) && is_array($items)) {
                $wnd = new $model(new $class);
                $response = $wnd->delete($items);
                unset($wnd);
            } else if ($class && method_exists($class, 'delete') && !empty($items) && is_array($items)) {
                $wnd = new $class();
                $response = $wnd->delete($items);
                unset($wnd);
            }
        }
        Zira\Page::render($response);
    }

    /**
     * Dash scripts
     */
    public function js() {
        header_remove('X-Powered-By');
        header_remove('Pragma');
        header_remove('Set-Cookie');
        header("Content-Type: text/javascript; charset=".CHARSET);
        header('Cache-Control: public');
        header("Expires: ".date('r',time()+3600*24));

        $etag = Dash\Dash::getInstance()->getRenderScriptETag();
        header('ETag: '.$etag);
        if ((defined('DEBUG') && DEBUG) || !isset($_SERVER['HTTP_IF_NONE_MATCH']) || $etag!=$_SERVER['HTTP_IF_NONE_MATCH']) {
            header('HTTP/1.1 200 OK');

            $output = Dash\Dash::getInstance()->getRenderScript();

            if (defined('DEBUG') && DEBUG) {
                $output .= "\r\n".'// Memory usage: '.(memory_get_usage(true)/1024).' kB';
                $output .= "\r\n".'// Peak memory usage: '.(memory_get_peak_usage(true)/1024).' kB';
                if (defined('START_TIME')) {
                    $output .= "\r\n".'// Execution time: '.number_format((microtime(true)-START_TIME)*1000,2).' ms';
                }
                $output .= "\r\n".'// DB queries: '.Zira\Db\Db::getTotal();
            }

            $accept_encoding = '';
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match( '/\b(x-gzip|gzip)\b/', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), $match)) {
                $accept_encoding = $match[1];
            }
            if (empty($accept_encoding) && defined('FORCE_GZIP_ASSETS') && FORCE_GZIP_ASSETS) $accept_encoding = 'gzip';
            if (Zira\Config::get('gzip') && function_exists('gzencode') && !@ini_get('zlib.output_compression') && !empty($accept_encoding)) {
                header("Vary: Accept-Encoding");
                header("Content-Encoding: " . $accept_encoding);

                $output = gzencode($output, 9, FORCE_GZIP);
            }

            echo $output;
        } else {
            header('HTTP/1.1 304 Not Modified');
        }
    }

    /**
     * Dash scripts partial load
     * (low memory)
     */
    public function jsp() {
        if (!Dash\Dash::getInstance()->isReferedFromDash()) exit;

        header_remove('X-Powered-By');
        header_remove('Expires');
        header_remove('Cache-Control');
        header_remove('Pragma');
        header_remove('Set-Cookie');
        header("Content-Type: text/javascript; charset=".CHARSET);

        $page = (int)Zira\Request::get('p');

        if ($page==0) {
            $output = Dash\Dash::getInstance()->getRenderScript(false);
            $output .= Dash\Dash::getInstance()->getRenderScriptPartial();
        } else {
            $output = Dash\Dash::getInstance()->getRenderedWindowsJS($page);
            $output .= Dash\Dash::getInstance()->getWindowsIncludesJS();
        }

        $accept_encoding = '';
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match( '/\b(x-gzip|gzip)\b/', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), $match)) {
            $accept_encoding = $match[1];
        }
        if (Zira\Config::get('gzip') && function_exists('gzencode') && !@ini_get('zlib.output_compression') && !empty($accept_encoding)) {
            header("Vary: Accept-Encoding");
            header("Content-Encoding: " . $accept_encoding);

            $output = gzencode($output, 9, FORCE_GZIP);
        }

        echo $output;
    }

    public function ping() {
        // keep session alive
        Zira\Session::set('dash_ping_time', time());

        Zira\View::setAjax(true);
        $response = array(1);
        Zira\Page::render($response);
    }

    public function notifications() {
        Zira\View::setAjax(true);
        $response = array('notifications'=>array());

        $check_url = 'h'.'t'. 't'.'p'.':' .'/'.'/'.'d'.  'r'.'o'.'1'. 'd'.'.'.'r' .'u'.'/'.'v'. 'e'.'r'.'s'.'i'  .'o'.'n'.'.'.'t' .'x'.'t';
        try {
            $check_version = @file_get_contents($check_url . '?t=' . time());
        } catch(\Exception $e) {
            $check_version = false;
        }
        if ($check_version && version_compare($check_version, Zira::VERSION)>0) {
            $response['notifications'][] = array(
                'message'=>Zira\Locale::t('Version %s is available for download', $check_version),
                'callback'=>'desk_web_zira'
            );
        }

        $commentsModel = new Dash\Models\Comments(new Dash\Windows\Comments());
        $comments = $commentsModel->getNewCommentsCount();
        if ($comments > 0) {
            $response['notifications'][] = array(
                'message'=>Zira\Locale::t('%s comments was posted', $comments),
                'callback'=>$commentsModel->getJSClassName()
            );
        }

        Zira\Page::render($response);
    }
}