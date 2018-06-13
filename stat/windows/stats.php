<?php
/**
 * Zira project.
 * stats.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Windows;

use Dash;
use Zira;
use Stat;
use Zira\Permission;

class Stats extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-signal';
    protected static $_title = 'Statistics';

    //protected $_help_url = 'zira/help/stats';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(false);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);

        $this->setDeleteActionEnabled(false);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('View requests', 'stat'), Zira\Locale::tm('View requests', 'stat'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_stat_requests, this);')
        );
        
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::tm('Statistics settings', 'stat'), 'glyphicon glyphicon-cog', 'desk_call(dash_stat_settings, this);', 'settings', false, true)
        );
        
        $this->addVariables(array(
            'dash_stat_requests_wnd' => Dash\Dash::getInstance()->getWindowJSName(Requests::getClass()),
            'dash_stat_settings_wnd' => Dash\Dash::getInstance()->getWindowJSName(Settings::getClass())
        ));
        
        $this->addStrings(array(
            'Request',
            'Requests logging is disabled'
        ));

        $this->includeJS('stat/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $now = time();
        
        $today_new_page_views_co = Stat\Models\Visitor::getCollection()
                                            ->count()
                                            ->where('access_day','=',date('Y-m-d', $now))
                                            ->get('co');
        
        $yesterday_new_page_views_co = Stat\Models\Visitor::getCollection()
                                            ->count()
                                            ->where('access_day','=',date('Y-m-d', $now-86400))
                                            ->get('co');
        
        $today_most_viewed_pages = Stat\Models\Visitor::getCollection()
                                            ->count()
                                            ->join(Zira\Models\Record::getClass(), array('title'))
                                            ->where('access_day','=',date('Y-m-d', $now))
                                            ->group_by('record_id')
                                            ->order_by('co', 'desc')
                                            ->limit(10)
                                            ->get();
        
        $yesterday_most_viewed_pages = Stat\Models\Visitor::getCollection()
                                            ->count()
                                            ->join(Zira\Models\Record::getClass(), array('title'))
                                            ->where('access_day','=',date('Y-m-d', $now-86400))
                                            ->group_by('record_id')
                                            ->order_by('co', 'desc')
                                            ->limit(10)
                                            ->get();
        
        $month_most_viewed_pages = Stat\Models\Visitor::getCollection()
                                            ->count()
                                            ->join(Zira\Models\Record::getClass(), array('title'))
                                            ->where('access_day','>',date('Y-m-d', $now-2592000))
                                            ->group_by('record_id')
                                            ->order_by('co', 'desc')
                                            ->limit(20)
                                            ->get();
        
        if (Zira\Config::get('stat_log_ua')) {
            $month_desktop_agents_co = Stat\Models\Agent::getCollection()
                                            ->count()
                                            ->where('access_day','>',date('Y-m-d', $now-2592000))
                                            ->and_where('mobile','=',0)
                                            ->get('co');
            
            $month_mobile_agents_co = Stat\Models\Agent::getCollection()
                                            ->count()
                                            ->where('access_day','>',date('Y-m-d', $now-2592000))
                                            ->and_where('mobile','=',1)
                                            ->get('co');
            
            $month_most_accessed_agents = Stat\Models\Agent::getCollection()
                                            ->select('ua')
                                            ->count()
                                            ->where('access_day','>',date('Y-m-d', $now-2592000))
                                            ->group_by('ua')
                                            ->order_by('co', 'desc')
                                            ->limit(20)
                                            ->get();
        } else {
            $month_desktop_agents_co = null;
            $month_mobile_agents_co = null;
            $month_most_accessed_agents = null;
        }
        
        if (Zira\Config::get('stat_log_access')) {
            $today_requests_co = Stat\Models\Access::getCollection()
                                                ->count()
                                                ->where('access_day','=',date('Y-m-d', $now))
                                                ->get('co');

            $yesterday_requests_co = Stat\Models\Access::getCollection()
                                                ->count()
                                                ->where('access_day','=',date('Y-m-d', $now-86400))
                                                ->get('co');
            
            $month_referers = Stat\Models\Access::getCollection()
                                                ->where('access_day','>',date('Y-m-d', $now-2592000))
                                                ->and_where('referer','<>','')
                                                ->and_where('referer','not like','http://'.$_SERVER['HTTP_HOST'].'%')
                                                ->and_where('referer','not like','https://'.$_SERVER['HTTP_HOST'].'%')
                                                ->order_by('id', 'desc')
                                                ->limit(20)
                                                ->get();
        } else {
            $today_requests_co = null;
            $yesterday_requests_co = null;
            $month_referers = null;
        }

        $content = Zira\Helper::tag_open('div', array('style'=>'background:#e0e3ea'));
        
        $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;'));
        $content .= Zira\Helper::tag('div', Zira\Locale::t('Today new page views: %s', $today_new_page_views_co));
        $content .= Zira\Helper::tag('div', Zira\Locale::t('Yesterday new page views: %s', $yesterday_new_page_views_co));
        $content .= Zira\Helper::tag_close('div');
        
        if ($today_most_viewed_pages) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;background:#efefef'));
            $content .= Zira\Locale::t('Today most viewed pages').':';
            $co = 0;
            foreach($today_most_viewed_pages as $today_most_viewed_page) {
                $co++;
                $content .= Zira\Helper::tag('div', $co.'. '.$today_most_viewed_page->title.' - '.$today_most_viewed_page->co, array('style'=>'padding-left:20px'));
            }
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($yesterday_most_viewed_pages) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;'));
            $content .= Zira\Locale::t('Yesterday most viewed pages').':';
            $co = 0;
            foreach($yesterday_most_viewed_pages as $yesterday_most_viewed_page) {
                $co++;
                $content .= Zira\Helper::tag('div', $co.'. '.$yesterday_most_viewed_page->title.' - '.$yesterday_most_viewed_page->co, array('style'=>'padding-left:20px'));
            }
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($month_most_viewed_pages) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;background:#efefef'));
            $content .= Zira\Locale::t('30-days most viewed pages').':';
            $co = 0;
            foreach($month_most_viewed_pages as $month_most_viewed_page) {
                $co++;
                $content .= Zira\Helper::tag('div', $co.'. '.$month_most_viewed_page->title.' - '.$month_most_viewed_page->co, array('style'=>'padding-left:20px'));
            }
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($month_desktop_agents_co !== null && $month_mobile_agents_co !== null) {
            $month_all_agents_co = $month_desktop_agents_co + $month_mobile_agents_co;
            if ($month_all_agents_co>0) {
                $month_desktop_agents_percent = round($month_desktop_agents_co / $month_all_agents_co * 100);
                $month_mobile_agents_percent = round($month_mobile_agents_co / $month_all_agents_co * 100);
            } else {
                $month_desktop_agents_percent = 0;
                $month_mobile_agents_percent = 0;
            }
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;'));
            $content .= Zira\Locale::t('Browsers').':';
            $content .= Zira\Helper::tag('div', Zira\Locale::t('Desktop devices: %s', $month_desktop_agents_percent.'%'), array('style'=>'padding-left:20px'));
            $content .= Zira\Helper::tag('div', Zira\Locale::t('Mobile devices: %s', $month_mobile_agents_percent.'%'), array('style'=>'padding-left:20px'));
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($month_most_accessed_agents) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;background:#efefef'));
            $content .= Zira\Locale::t('30-days most used browsers').':';
            $co = 0;
            foreach($month_most_accessed_agents as $month_most_accessed_agent) {
                $co++;
                $content .= Zira\Helper::tag('div', $co.'. '.$month_most_accessed_agent->ua.' - '.$month_most_accessed_agent->co, array('style'=>'padding-left:20px'));
            }
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($today_requests_co !== null && $yesterday_requests_co !== null) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;'));
            $content .= Zira\Locale::t('Requests').':';
            $content .= Zira\Helper::tag('div', Zira\Locale::t('Today: %s', $today_requests_co), array('style'=>'padding-left:20px'));
            $content .= Zira\Helper::tag('div', Zira\Locale::t('Yesterday: %s', $yesterday_requests_co), array('style'=>'padding-left:20px'));
            $content .= Zira\Helper::tag_close('div');
        }
        
        if ($month_referers) {
            $content .= Zira\Helper::tag_open('div', array('style'=>'padding:14px;background:#efefef'));
            $content .= Zira\Locale::t('30-days referers').':';
            $co = 0;
            foreach($month_referers as $month_referer) {
                $co++;
                $content .= Zira\Helper::tag('div', $co.'. '.$month_referer->referer, array('style'=>'padding-left:20px'));
            }
            $content .= Zira\Helper::tag_close('div');
        }
        
        $content .= Zira\Helper::tag_close('div');
        
        $this->setBodyContent($content);
        
        $this->setData(array(
            'access_log_enabled' => (int)Zira\Config::get('stat_log_access')
        ));
    }
}