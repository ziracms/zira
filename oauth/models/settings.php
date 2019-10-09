<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Oauth\Models;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Oauth\Forms\Settings();
        if ($form->isValid()) {
            $options = array(
                'oauth_fb_app_id'=>'string',
                'oauth_fb_app_secret'=>'string',
                'oauth_fb_on'=>'int',
                'oauth_fb_page_url'=>'string',
                'oauth_vk_app_id'=>'string',
                'oauth_vk_app_secret'=>'string',
                'oauth_vk_on'=>'int',
                'oauth_vk_group_id'=>'string'
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
                $optionObj->module = 'oauth';
                $optionObj->save();
            }

            Zira\Cache::clear(true);

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}