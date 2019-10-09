<?php
/**
 * Zira project.
 * category.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Category extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\Category();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ((empty($id) && !Permission::check(Permission::TO_CREATE_RECORDS)) ||
                (!empty($id) && !Permission::check(Permission::TO_EDIT_RECORDS))
            ) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $root = trim((string)$form->getValue('root'),'/');

            if (!empty($root)) {
                $parent = Zira\Models\Category::getCollection()
                                    ->where('name','=',$root)
                                    ->get(0);
                if (!$parent) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }
                $parent_id = $parent->id;
            } else {
                $parent_id = Zira\Category::ROOT_CATEGORY_ID;
            }

            if (!empty($root)) $root .= '/';
            $name = $root.$form->getValue('name');

            if (!empty($id)) {
                $category = new Zira\Models\Category($id);

                $rows = Zira\Models\Category::getCollection()
                                ->where('name','like',$category->name.'/%')
                                ->get(null, true);
                foreach($rows as $row) {
                    if (preg_match('/^'.addcslashes(preg_quote($category->name),'/').'\/(.+)$/u',$row['name'],$m)) {
                        $row['name'] = $name . '/' . $m[1];
                        $obj = new Zira\Models\Category();
                        $obj->loadFromArray($row);
                        $obj->save();
                    }
                }
            } else {
                $category = new Zira\Models\Category();
            }

            $category->name = $name;
            $category->title = $form->getValue('title');
            $category->layout = $form->getValue('layout');
            $category->parent_id = $parent_id;
            $category->access_check = (int)$form->getValue('access_check');
            $category->gallery_check = (int)$form->getValue('gallery_check');
            $category->files_check = (int)$form->getValue('files_check');
            $category->audio_check = (int)$form->getValue('audio_check');
            $category->video_check = (int)$form->getValue('video_check');
            $category->comments_enabled = (int)$form->getValue('comments_enabled');
            
            $category->save();

            Zira\Cache::clear();
            Zira\Models\Option::raiseVersion();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}