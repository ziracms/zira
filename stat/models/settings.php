<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Models;

use Zira;
use Dash;
use Stat;
use Zira\Permission;

class Settings extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Stat\Forms\Settings();
        if ($form->isValid()) {
            $options = array(
                'stat_log_ua' => 'int',
                'stat_log_access' => 'int',
                'stat_views_preview' => 'int',
                'stat_exclude_bots' => 'int'
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
                $optionObj->module = 'forum';
                $optionObj->save();
            }

            Zira\Models\Option::raiseVersion();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}