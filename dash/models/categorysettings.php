<?php
/**
 * Zira project.
 * categorysettings.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Categorysettings extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Categorysettings();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            if (empty($id)) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $category = new Zira\Models\Category($id);
            if (!$category->loaded()) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $category->slider_enabled = (int)$form->getValue('slider_enabled');
            $category->gallery_enabled = (int)$form->getValue('gallery_enabled');
            $category->files_enabled = (int)$form->getValue('files_enabled');
            $category->audio_enabled = (int)$form->getValue('audio_enabled');
            $category->video_enabled = (int)$form->getValue('video_enabled');
            $category->rating_enabled = (int)$form->getValue('rating_enabled');
            $category->display_author = (int)$form->getValue('display_author');
            $category->display_date = (int)$form->getValue('display_date');
            $category->records_list = (int)$form->getValue('records_list');
            $category->save();

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}