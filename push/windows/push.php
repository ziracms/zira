<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Push extends Dash\Windows\Window {
    const LIMIT = 1;
    protected static $_icon_class = 'glyphicon glyphicon-cloud-upload';
    protected static $_title = 'Push notifications';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(false);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::tm('Start sending', 'push'), Zira\Locale::tm('Start sending', 'push'), 'glyphicon glyphicon-cloud-upload', 'desk_call(dash_push_push_begin, this);', 'begin', true, false)
        );
        
        $this->addDefaultOnLoadScript(
            'desk_call(dash_push_push_load, this);'
        );

        $this->setData(array(
            'subscribers' => 0,
            'offset' => 0,
            'language' => ''
        ));
        
        $this->addStrings(array(
            'Start sending',
            'Successfully finished. Notifications sent:'
        ));
        
        $this->includeJS('push/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $subscribers = \Push\Models\Subscription::getCollection()
                                                ->count()
                                                ->where('active','=',1)
                                                ->get('co');

        $data = array();
        $form = new \Push\Forms\Send();
        $form->setValues($data);
        $this->setBodyContent($form);

        $lang_subscribers = array();
        if (count(Zira\Config::get('languages'))>1) {
            $menu = array(
                //$this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown())
            );
            
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_push_push_language, this, element);', 'language', false, array('language'=>$lang_key));

                $lang_subscribers[$lang_key] = \Push\Models\Subscription::getCollection()
                                                ->count()
                                                ->where('active','=',1)
                                                ->and_where('language', '=', $lang_key)
                                                ->get('co');
                
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
            
            $this->setMenuItems($menu);
        }
        
        
        $this->setData(array(
            'subscribers' => $subscribers,
            'lang_subscribers' => $lang_subscribers,
            'offset' => 0,
            'language' => ''
        ));
    }
}