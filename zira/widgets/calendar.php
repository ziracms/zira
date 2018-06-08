<?php
/**
 * Zira project.
 * calendar.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Calendar extends Zira\Widget {
    protected $_title = 'Calendar';
    protected $_span_class = null;
    protected static $_js_added = false;

    protected function _init() {
        $this->setDynamic(false);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
        
        if (!self::$_js_added) {
            $start_dow = Zira\Locale::getLanguage() == 'ru' ? 1 : 0;
            $search_url = Zira\Helper::url('search').'?text=';
            $js = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
            $js .= 'jQuery(document).ready(function(){';
            $js .= 'zira_calendar(\'.calendar-widget-wrapper\', '.$start_dow.', function(date){';
            $js .= 'var url = \''.$search_url.'\';';
            $js .= 'var day = date.getDate();';
            $js .= 'var month = date.getMonth();';
            $js .= 'var year = date.getFullYear();';
            $js .= 'var search = year+\'-\'+(\'0\'+(month+1)).slice(-2)+\'-\'+(\'0\'+day).slice(-2);';
            $js .= 'window.location.href=url+search;';
            $js .= '});';
            $js .= '});';
            $js .= Zira\Helper::tag_close('script')."\r\n";
            Zira\View::addBodyBottomScript($js);
            self::$_js_added = true;
        }
    }
    
    protected function _render() {
        Zira\View::renderView(array('class'=>$this->_span_class), 'zira/widgets/calendar');
    }
}