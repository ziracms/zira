<?php
/**
 * Zira project.
 * recordmeta.php
 * (c)2016 http://dro1d.ru
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

            $record->meta_title = $form->getValue('meta_title');
            $record->meta_keywords = $form->getValue('meta_keywords');
            $record->meta_description = $form->getValue('meta_description');
            // keep draft
            //$record->modified_date = date('Y-m-d H:i:s');

            $record->save();
            Zira\Models\Search::indexRecord($record);

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}