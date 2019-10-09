<?php
/**
 * Zira project.
 * holiday.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Holiday;

use Zira;
use Dash;

class Holiday {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function onActivate() {
        Zira\Assets::registerCSSAsset('holiday/holiday.css');
        Zira\Assets::registerJSAsset('holiday/holiday.js');
    }

    public function onDeactivate() {
        Zira\Assets::unregisterCSSAsset('holiday/holiday.css');
        Zira\Assets::unregisterJSAsset('holiday/holiday.js');
    }

    public function beforeDispatch() {
        Zira\Assets::registerCSSAsset('holiday/holiday.css');
        Zira\Assets::registerJSAsset('holiday/holiday.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('holiday/holiday.css');
        Zira\View::addScript('holiday/holiday.js');

        if (ENABLE_CONFIG_DATABASE && Dash\Dash::getInstance()->isPanelEnabled() && Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) && Zira\Permission::check(Zira\Permission::TO_CHANGE_LAYOUT)) {
            Dash\Dash::loadDashLanguage();
            Dash\Dash::getInstance()->addPanelModulesGroupItem('glyphicon glyphicon-gift', Zira\Locale::tm('Holidays', 'holiday', null, Dash\Dash::getDashLanguage()), null, 'holidaysWindow()');
            Dash\Dash::getInstance()->registerModuleWindowClass('holidaysWindow', 'Holiday\Windows\Holidays', 'Holiday\Models\Holidays');
            Dash\Dash::getInstance()->registerModuleWindowClass('holidayWindow', 'Holiday\Windows\Holiday', 'Holiday\Models\Holidays');
            Dash\Dash::getInstance()->registerModuleWindowClass('holidaySettingsWindow', 'Holiday\Windows\Settings', 'Holiday\Models\Settings');
            Dash\Dash::unloadDashLanguage();
        }
        
        if (!Zira\View::isAjax()) {
            $holidaysData = Zira\Cache::getArray('holidays');
            if ($holidaysData===false) {
                $holidaysData = array();
                $holidays = \Holiday\Models\Holiday::getHolidays();
                foreach($holidays as $holiday) {
                    $holidaysData[$holiday->cdate] = array(
                        $holiday->title,
                        $holiday->description,
                        $holiday->image,
                        $holiday->audio,
                        $holiday->cls
                    );
                }
                Zira\Cache::setArray('holidays', $holidaysData);
            }
            if (!empty($holidaysData)) {
                $this->processHolidays($holidaysData);
            }
            
            if (Zira\Router::getModule()!='dash' && !Dash\Dash::isFrame()) {
                $js_scripts = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
                $is_new_year = date('n') == 1 ? 1 : 0;
                $is_new_year_mode = Zira\Config::get('holiday_ny_mode') ? 1 : 0;
                $is_snowing = Zira\Config::get('holiday_snow') ? 1 : 0;
                $js_scripts .= 'zira_holiday_ny_mode = '.$is_new_year_mode.';';
                $js_scripts .= 'zira_holiday_snow = '.$is_snowing.';';
                $js_scripts .= 'zira_holiday_is_ny = '.$is_new_year.';';
                $js_scripts .= Zira\Helper::tag_close('script');
                Zira\View::addBodyBottomScript($js_scripts);
            }
        }
    }
    
    protected function processHolidays($holidays, $day=null, $month=null) {
        if (Zira\Router::getModule()=='dash' || Dash\Dash::isFrame()) return;
        if (Zira\Cookie::get('zira_celebration') && empty($day) && empty($month)) return;
        
        if (empty($holidays) || !is_array($holidays)) return;
        
        if (empty($day) || empty($month)) {
            $time = time();
            $day = date('j',$time);
            $month = date('n',$time);
        }

        if (array_key_exists($day.'.'.$month, $holidays) && is_array($holidays[$day.'.'.$month]) && count($holidays[$day.'.'.$month])==5) {
            $_celebration_date = Zira\Locale::t($holidays[$day.'.'.$month][0]);
            $_celebration_message = Zira\Locale::t($holidays[$day.'.'.$month][1]);
            $_celebration_image = $holidays[$day.'.'.$month][2];
            $_celebration_audio = $holidays[$day.'.'.$month][3];
            $_celebration_class = $holidays[$day.'.'.$month][4];
            
            Zira\View::addHTML(self::celebrate($_celebration_date, $_celebration_message, $_celebration_image, $_celebration_audio, $_celebration_class), Zira\View::VAR_BODY_TOP);
        }
    }
    
    public static function celebrate($date, $message='', $image='', $audio='', $class='', $preview=false) {
        if (headers_sent()) return;
        if (!empty($audio)) $audio = Zira\Helper::baseUrl($audio);
        if (!$preview) Zira\Cookie::set('zira_celebration', '1', 86400);
        $html = Zira\Helper::tag('div',null,array('class'=>'zira-celebration-bg'));
        $html .= Zira\Helper::tag_open('div',array('class'=>'zira-celebration '.$class, 'title'=>$date, 'data-asrc'=>$audio));
        $html .= Zira\Helper::tag('div', $date, array('class'=>'celebration-date'));
        if (!empty($image)) {
            $html .= Zira\Helper::tag_short('img', array('src'=>Zira\Helper::baseUrl($image), 'alt'=>$message, 'title'=>$message, 'class'=>'celebration-image'));
        }
        $html .= Zira\Helper::tag('div', $message, array('class'=>'celebration-message'));
        $html .= Zira\Helper::tag('div', null, array('class'=>'celebration-close'));
        $html .= Zira\Helper::tag_close('div');
        return $html;
    }
}