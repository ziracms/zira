<?php
/**
 * Zira project.
 * recordscalendar.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Recordscalendar extends Calendar {
    protected $_title = 'Calendar of publications';
    protected $_span_class = 'records-calendar';
    protected static $_js_added = false;
    
    protected function _init() {
        parent::_init();
        if (!self::$_js_added) {
            $ajax_url = Zira\Helper::url('zira/records/calendar');
            $js = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
            $js .= 'jQuery(document).ready(function(){';
            $js .= 'if (jQuery(\'.'.$this->_span_class.'\').length>0){';
            $js .= 'jQuery(\'.'.$this->_span_class.'\').on(\'zira_calendar_month_change\',function(e, month, year){';
            $js .= 'jQuery(this).find(\'.zira-calendar-day\').parent().removeClass(\'today\');';
            $js .= 'var now = new Date();';
            $js .= 'if (now.getMonth()<month || now.getFullYear()<year) return;';
            $js .= 'jQuery.post(\''.$ajax_url.'\',{';
            $js .= 'month: month, year: year';
            $js .= '},zira_bind(this,function(response){';
            $js .= 'if (!response || typeof response.days == "undefined") return;';
            $js .= 'for(var i=0; i<response.days.length; i++){';
            $js .= 'jQuery(this).find(\'.zira-calendar-day[data-month=\'+month+\'][data-day=\'+response.days[i]+\']\').parent(\'li\').addClass(\'today\');';
            $js .= '}';
            $js .= '}),\'json\');';
            $js .= '});';
            $js .= 'jQuery(\'.'.$this->_span_class.'\').trigger(\'ready\');';
            $js .= '}';
            $js .= '});';
            $js .= Zira\Helper::tag_close('script')."\r\n";
            Zira\View::addBodyBottomScript($js);
            self::$_js_added = true;
        }
    }
}