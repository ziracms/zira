<?php
/**
 * Zira project.
 * recordmeta.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordmeta extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\Recordmeta();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            $record = new Zira\Models\Record($id);
            if (!$record->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            if (!$record->meta_title && !$record->meta_keywords && !$record->meta_description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $tags = trim($form->getValue('record_tags'));
            $tags_arr = explode(',', $tags);
            $tags_str = '';
            $tags_added = array();
            $co = 0;
            foreach($tags_arr as $tag) {
                $tag = trim($tag);
                if (empty($tag) || in_array($tag, $tags_added)) continue;
                $tags_added []= $tag;
                $tag_add = '';
                if ($co > 0) $tag_add .= ',';
                $tag_add .= $tag;
                if (mb_strlen($tags_str, CHARSET) + mb_strlen($tag_add, CHARSET) < 255) {
                    $tags_str .= $tag_add;
                    $co++;
                }
            }

            $record->tags = $tags_str;
            $record->meta_title = $form->getValue('meta_title');
            $record->meta_keywords = $form->getValue('meta_keywords');
            $record->meta_description = $form->getValue('meta_description');
            
            // keep draft
            //$record->modified_date = date('Y-m-d H:i:s');

            $record->save();
            Zira\Models\Search::indexRecord($record);
            Zira\Models\Tag::addTags($record->id, $record->language, $tags_added);

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}