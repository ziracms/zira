<?php
/**
 * Zira project.
 * mailing.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Mailing extends Window {
    const LIMIT = 10;

    protected static $_icon_class = 'glyphicon glyphicon-envelope';
    protected static $_title = 'Mailing';

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButtonGroup(array(
                $this->createToolbarButton(Zira\Locale::t('Email').' (?)',Zira\Locale::t('Subscribers'), null, 'desk_call(dash_mailing_type_email, this);', 'subscribers', true),
                $this->createToolbarButton(Zira\Locale::t('Message').' (?)',Zira\Locale::t('Users'), null, 'desk_call(dash_mailing_type_message, this);', 'users', true)
            ))
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Start mailing'), Zira\Locale::t('Start mailing'), 'glyphicon glyphicon-flash', 'desk_call(dash_mialing_send, this);', 'mail', true, true)
        );
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_mailing_load, this);'
            )
        );

        $this->setData(array(
            'users' => 0,
            'subscribers' => 0,
            'offset' => 0,
            'language' => ''
        ));

        $this->addStrings(array(
            'Message',
            'Email',
            'Successfully finished. Emails sent:',
            'Successfully finished. Messages sent:'
        ));

        $this->includeJS('dash/mailing');
    }

    public function load() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $users = Zira\Models\User::getCollection()
                                ->count()
                                ->where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->get('co');

        $subscribers = Zira\Models\User::getCollection()
                                ->count()
                                ->where('verified','=',Zira\Models\User::STATUS_VERIFIED)
                                ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->and_where('subscribed','=',Zira\Models\User::STATUS_SUBSCRIBED)
                                ->get('co');

        $form = new \Dash\Forms\Mailing();
        $form->setValues(array('type'=>'email'));
        $this->setBodyContent($form);

        $lang_users = array();
        $lang_subscribers = array();
        if (count(Zira\Config::get('languages'))>1) {
            $menu = array(
                //$this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown())
            );

            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_mailing_language, this, element);', 'language', false, array('language'=>$lang_key));

                $lang_users[$lang_key] = Zira\Models\User::getCollection()
                                ->count()
                                ->where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->and_where('language', '=', $lang_key)
                                ->get('co');

                $lang_subscribers[$lang_key] = Zira\Models\User::getCollection()
                                ->count()
                                ->where('verified','=',Zira\Models\User::STATUS_VERIFIED)
                                ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->and_where('subscribed','=',Zira\Models\User::STATUS_SUBSCRIBED)
                                ->and_where('language', '=', $lang_key)
                                ->get('co');
            
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);

            $this->setMenuItems($menu);
        }
        

        $this->setData(array(
            'users' => $users,
            'subscribers' => $subscribers,
            'lang_users' => $lang_users,
            'lang_subscribers' => $lang_subscribers,
            'offset' => 0,
            'language' => ''
        ));
    }
}