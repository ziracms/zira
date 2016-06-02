<?php
/**
 * Zira project.
 * usersettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Usersettings extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Usersettings();
        if ($form->isValid()) {
            $options = array(
                'user_photo_min_width'=>'int',
                'user_photo_min_height'=>'int',
                'user_photo_max_width'=>'int',
                'user_photo_max_height'=>'int',
                'user_thumb_width'=>'int',
                'user_thumb_height'=>'int',
                'user_signup_allow'=>'int',
                'user_profile_view_allow'=>'int',
                'user_login_change_allow'=>'int',
                'user_email_verify'=>'int',
                'user_check_ua'=>'int'
            );

            $config_ids = array();
            $user_configs = Zira\Models\Option::getCollection()->get();
            foreach($user_configs as $user_config) {
                $config_ids[$user_config->name] = $user_config->id;
            }

            foreach($options as $option=>$type) {
                if (!array_key_exists($option, $config_ids)) {
                    $optionObj = new Zira\Models\Option();
                } else {
                    $optionObj = new Zira\Models\Option($config_ids[$option]);
                }
                $optionObj->name = $option;
                $value = $form->getValue($option);

                if ($type=='int') $value=intval($value);

                $optionObj->value = $value;
                $optionObj->module = 'zira';
                $optionObj->save();
            }

            Zira\Models\Option::raiseVersion();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}