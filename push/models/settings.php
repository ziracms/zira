<?php
/**
 * Zira project.
 * settings.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Models;

use Zira;
use Dash;
use Zira\Permission;

class Settings extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Push\Forms\Settings();
        if ($form->isValid()) {
            $options = array(
                'push_priv_key'=>'string',
                'push_pub_key'=>'string',
                'push_subscribe_onload_on'=>'int'
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
                $optionObj->module = 'push';
                $optionObj->save();
            }

            Zira\Cache::clear(true);

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}