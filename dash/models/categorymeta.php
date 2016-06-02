<?php
/**
 * Zira project.
 * categorymeta.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Categorymeta extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\Categorymeta();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            $category = new Zira\Models\Category($id);
            if (!$category->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            if (!$category->meta_title && !$category->meta_keywords && !$category->meta_description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $category->meta_title = $form->getValue('meta_title');
            $category->meta_keywords = $form->getValue('meta_keywords');
            $category->meta_description = $form->getValue('meta_description');

            $category->save();

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'));
        } else {
            return array('error'=>$form->getError());
        }
    }
}