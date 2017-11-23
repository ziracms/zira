<?php
/**
 * Zira project.
 * recordsettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordsettings extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Recordsettings();
        if ($form->isValid()) {
            $options = array(
                'thumbs_width'=>'int',
                'thumbs_height'=>'int',
                'slider_enabled'=>'int',
                'gallery_enabled'=>'int',
                'files_enabled'=>'int',
                'audio_enabled'=>'int',
                'video_enabled'=>'int',
                'rating_enabled'=>'int',
                'display_author'=>'int',
                'display_date'=>'int',
                'jpeg_quality'=>'int',
                'create_thumbnails'=>'int'
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