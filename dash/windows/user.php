<?php
/**
 * Zira project.
 * user.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class User extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-user';
    protected static $_title = 'User';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Change avatar'), 'glyphicon glyphicon-picture', 'desk_call(dash_user_image_select, this);', 'image')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Delete avatar'), 'glyphicon glyphicon-ban-circle', 'desk_call(dash_user_delete_image, this);', 'noimage', true)
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButtonGroup(array(
                $this->createToolbarButton(Zira\Locale::t('verified'), Zira\Locale::t('verified'), 'glyphicon glyphicon-ban-circle', 'desk_call(dash_user_verified, this);', 'verified'),
                $this->createToolbarButton(Zira\Locale::t('active'), Zira\Locale::t('active'), 'glyphicon glyphicon-ban-circle', 'desk_call(dash_user_active, this);', 'active')
                ),
                true
            )
        );

        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(dash_user_load, this);'
            )
        );
        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_user_drop, this, element);'
            )
        );

        $this->addVariables(array(
            'dash_user_status_active' => Zira\Models\User::STATUS_ACTIVE,
            'dash_user_status_not_active' => Zira\Models\User::STATUS_NOT_ACTIVE,
            'dash_user_status_verified' => Zira\Models\User::STATUS_VERIFIED,
            'dash_user_status_not_verified' => Zira\Models\User::STATUS_NOT_VERIFIED,
            'dash_user_profile_nophoto_src' => Zira\User::getProfileNoPhotoUrl()
        ), true);

        $this->includeJS('dash/user');
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if ((!Permission::check(Permission::TO_CREATE_USERS) && !Permission::check(Permission::TO_EDIT_USERS)) ||
            (empty($this->item) && !Permission::check(Permission::TO_CREATE_USERS)) ||
            (!empty($this->item) && !Permission::check(Permission::TO_EDIT_USERS))
        ) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        if (!empty($this->item)) $this->setData(array('items'=>array($this->item)));

        $form = new \Dash\Forms\User();

        if (!empty($this->item)) {
            $user = new Zira\Models\User($this->item);
            if (!$user->loaded()) {
                return array('error'=>Zira\Locale::t('An error occurred'));
            }
            $this->setTitle(Zira\User::getProfileName($user));
            $userData = $user->toArray();
            $userData['password'] = '';
            $userData['dob'] = $form->prepareDatepickerDate($userData['dob']);
            $form->setValues($userData);

            $thumb=Zira\User::getProfilePhotoThumb($user, Zira\User::getProfileNoPhotoUrl());
        } else {
            $this->setTitle(Zira\Locale::t('New user'));
            $form->setValue('group_id', Zira\User::GROUP_USER);

            $thumb=Zira\User::getProfileNoPhotoUrl();
        }

        $thumb_width = Zira\Config::get('user_thumb_width');
        $thumb_height = Zira\Config::get('user_thumb_height');
        $sidebar_image = Zira\Helper::tag_short('img', array('class'=>'dashwindow-userthumb-selector','src'=>$thumb,'width'=>$thumb_width,'height'=>$thumb_height,'style'=>'display:block;margin:20px auto;max-width:100%;height:auto;cursor:pointer'));
        $sidebar_image .= Zira\Helper::tag('a',Zira\Locale::t('Delete'), array('href'=>'javascript:void(0)','style'=>'text-align:center','class'=>'dashwindow-userthumb-delete disabled'));
        $this->setSidebarContent($sidebar_image);
        $this->setBodyContent($form);
    }
}