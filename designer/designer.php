<?php
/**
 * Zira project.
 * designer.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer;

use Zira;
use Dash;

class Designer {
    private static $_instance;
    
    const CACHE_KEY = 'designer';
    
    protected static $_insert_inline = true;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public static function setInsertInline($inline) {
        self::$_insert_inline = (bool) $inline;
    }

    public function bootstrap() {
        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-picture', Zira\Locale::tm('Themes designer', 'designer', null, Dash\Dash::getDashLanguage()), null, 'stylesWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('stylesWindow', 'Designer\Windows\Styles', 'Designer\Models\Styles');
            Dash\Dash::getInstance()->registerModuleWindowClass('styleWindow', 'Designer\Windows\Style', 'Designer\Models\Styles');
            Dash\Dash::getInstance()->registerModuleWindowClass('designerWindow', 'Designer\Windows\Designer', 'Designer\Models\Designer');
            Dash\Dash::unloadDashLanguage();
        }
        
        if (self::isIE8()) {
            self::setInsertInline(false);
        }
        
        if (Zira\Router::getModule() != 'designer' && Zira\Router::getModule() != 'dash') {
            if (self::$_insert_inline) {
                Zira\View::registerRenderHook($this, 'applyStyle');
            } else {
                $style = Zira\Helper::tag_short('link', array(
                    'rel' => 'stylesheet',
                    'type' => 'text/css',
                    'href' => Zira\Helper::url('style')
                ));
                Zira\View::addHtml($style, Zira\View::VAR_HEAD_BOTTOM);
            }
        }
    }
    
    public function beforeDispatch() {
        Zira\Router::addRoute('style','designer/index/index');
    }
    
    protected static function isIE8() {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return (preg_match('/msie\s([\d]+)/i', $ua, $m) && $m[1]<=8);
    }
    
    public static function getStyle() {
        $styles = Zira\Cache::getObject(self::CACHE_KEY.'.'.Zira\View::getTheme().'.'.Zira\Locale::getLanguage());
        if (!$styles) {
            $styles = \Designer\Models\Style::getCollection()
                        ->open_query()
                        ->where('theme', '=', Zira\View::getTheme())
                        ->and_where('language','is',null)
                        ->and_where('active', '=', 1)
                        ->order_by('date_created', 'DESC')
                        ->close_query()
                        ->union()
                        ->open_query()
                        ->where('theme', '=', Zira\View::getTheme())
                        ->and_where('language','=',Zira\Locale::getLanguage())
                        ->and_where('active', '=', 1)
                        ->order_by('date_created', 'DESC')
                        ->close_query()
                        ->merge()
                        ->order_by('date_created', 'DESC')
                        ->get();
            
            Zira\Cache::setObject(self::CACHE_KEY.'.'.Zira\View::getTheme().'.'.Zira\Locale::getLanguage(), $styles);
        }
        
        $active_style = null;
        
        $category_id = null;
        if (!Zira\Router::getRequest()) {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        } else if (Zira\Category::current()) {
            $chain = Zira\Category::chain();
            $category_id = array();
            foreach($chain as $row) {
                $category_id[]=$row->id;
            }
        }
        
        foreach ($styles as $style) {
            if ($style->category_id!==null) {
                if (!is_array($category_id) && $style->category_id!=$category_id) continue;
                else if (is_array($category_id) && !in_array($style->category_id,$category_id)) continue;
            }
            if ($style->record_id && $style->record_id!=Zira\Page::getRecordId()) continue;
            if ($style->url && strlen($style->url)>0) {
                $request = urldecode(Zira\Router::getRequest());
                if ($style->url != $request && (
                    strlen($style->url)<=2 || 
                    substr($style->url, -2) != '/*' 
                )) continue;
                if ($style->url != $request && ( 
                    strlen($style->url)<=2 ||
                    substr($style->url, -2) != '/*' ||
                    mb_strpos($request.'/', substr($style->url, 0, strlen($style->url)-1), 0, CHARSET)!==0
                )) continue;
            }
            if ($style->filter && ((
                $style->filter == \Designer\Models\Style::STATUS_FILTER_RECORD &&
                Zira\Page::getRecordId()===null
            ) || (
                $style->filter == \Designer\Models\Style::STATUS_FILTER_CATEGORY &&
                (!Zira\Category::current() || Zira\Category::param() || Zira\Page::getRecordId()!==null)
            ) || (
                $style->filter == \Designer\Models\Style::STATUS_FILTER_CATEGORY_AND_RECORD &&
                !Zira\Category::current() && Zira\Page::getRecordId()===null
            ))) {
                continue;
            }
            
            $active_style = $style;
            break;
        }
        
        if (!$active_style) return;
        
        return $active_style->content;
    }
    
    public static function applyStyle() {
        $content = self::getStyle();
        if (empty($content)) return;
        
        $html = Zira\Helper::tag_open('style');
        $html .= $content;
        $html .= Zira\Helper::tag_close('style');

        Zira\View::addHTML($html, Zira\View::VAR_HEAD_BOTTOM);
    }
}