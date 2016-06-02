<?php
/**
 * Zira project.
 * contacts.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Contacts extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Contacts();
        if ($form->isValid()) {
            $options = array(
                'contact_name' => 'string',
                'contact_address' => 'string',
                'contact_image' => 'string',
                'feedback_email'=>'string',
                'contact_phone' => 'string',
                'contact_info' => 'string',
                'contact_fb' => 'string',
                'contact_gp' => 'string',
                'contact_tw' => 'string',
                'contact_vk' => 'string',
                'contact_google_map' => 'int',
                'contact_yandex_map' => 'int',
                'contact_email_public' => 'int',
                'google_map_key' => 'string',
                'yandex_map_key' => 'string',
                'maps_latitude' => 'string',
                'maps_longitude' => 'string'
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