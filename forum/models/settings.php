<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Settings extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Forum\Forms\Settings();
        if ($form->isValid()) {
            $options = array(
                'forum_layout' => 'string',
                'forum_title' => 'string',
                'forum_description' => 'string',
                'forum_meta_title' => 'string',
                'forum_meta_description' => 'string',
                'forum_meta_keywords' => 'string',
                'forum_limit' => 'int',
                'forum_min_chars' => 'int',
                'forum_file_uploads' => 'int',
                'forum_file_max_size' => 'int',
                'forum_file_ext' => 'string',
                'forum_moderate' => 'int',
                'forum_notify_email' => 'string',
                'forum_threads_sorting' => 'string'
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