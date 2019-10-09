<?php
/**
 * Zira project.
 * mailsettings.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Mailsettings extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Mailsettings();
        if ($form->isValid()) {
            $options = array(
                'use_smtp'=>'int',
                'smtp_host'=>'string',
                'smtp_port'=>'int',
                'smtp_secure'=>'string',
                'smtp_username'=>'string',
                'smtp_password'=>'string',
                'email_from'=>'string',
                'email_from_name'=>'string',
                'user_email_confirmation_message'=>'string',
                'user_password_recovery_message'=>'string',
                'user_new_password_message'=>'string',
                'comment_notification_message'=>'string',
                'feedback_message'=>'string',
                'new_message_notification'=>'string'
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
                else if ($type=='string') $value = str_replace("\r\n","\n",$value);

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