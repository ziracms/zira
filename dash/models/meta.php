<?php
/**
 * Zira project.
 * meta.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Meta extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Meta();
        if ($form->isValid()) {
            $options = array(
                'layout' => 'string',
                'site_name' => 'string',
                'site_slogan' => 'string',
                'site_logo' => 'string',
                'site_favicon' => 'string',
                'site_logo_width' => 'int',
                'site_logo_height' => 'int',
                'records_limit' => 'int',
                'widget_records_limit' => 'int',
                'category_childs_list' => 'int',
                'site_title' => 'string',
                'site_keywords' => 'string',
                'site_description' => 'string',
                'site_copyright' => 'string',
                'site_window_title' => 'int',
                'enable_pagination' => 'int',
                'gallery_check' => 'int',
                'files_check' => 'int',
                'audio_check' => 'int',
                'video_check' => 'int',
                'comments_enabled'=>'int',
                'site_scroll_effects'=>'int',
                'site_parse_images'=>'int'
            );

            $config_ids = array();
            $user_configs = Zira\Models\Option::getCollection()->get();
            foreach($user_configs as $user_config) {
                $config_ids[$user_config->name] = $user_config->id;
            }

            $logo = $form->getValue('site_logo');
            if (!empty($logo) && ($size = @getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $logo))!=false) {
                $form->setValue('site_logo_width', $size[0]);
                $form->setValue('site_logo_height', $size[1]);
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