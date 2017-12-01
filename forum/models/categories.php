<?php
/**
 * Zira project.
 * categories.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Categories extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Forum\Forms\Category();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            if ($id) {
                $category = new Forum\Models\Category($id);
                if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = Forum\Models\Category::getCollection()->max('sort_order')->get('mx');

                $category = new Forum\Models\Category();
                $category->sort_order = ++$max_order;
            }
            $category->title = $form->getValue('title');
            $layout = $form->getValue('layout');
            $category->layout = !empty($layout) ? $layout : null;
            $description = $form->getValue('description');
            $category->description = !empty($description) ? $description : null;
            $meta_title = $form->getValue('meta_title');
            $category->meta_title = !empty($meta_title) ? $meta_title : null;
            $meta_description = $form->getValue('meta_description');
            $category->meta_description = !empty($meta_description) ? $meta_description : null;
            $meta_keywords = $form->getValue('meta_keywords');
            $category->meta_keywords = !empty($meta_keywords) ? $meta_keywords : null;
            $category->access_check = (int)$form->getValue('access_check') ? 1 : 0;
            
            $language = $form->getValue('language');
            if (empty($language)) $language = null;
            $category->language = $language;

            $category->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $categories = array();
        foreach($data as $category_id) {
            $category = new Forum\Models\Category($category_id);
            if (!$category->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };

            $co = Forum\Models\Forum::getCollection()
                                ->count()
                                ->where('category_id', '=', $category_id)
                                ->get('co');
            if ($co>0) return array('error' => Zira\Locale::tm('Category "%s" has forums.','forum', $category->title));

            $categories []= $category;
        }

        foreach($categories as $category) {
            $category->delete();
        }

        return array('reload' => $this->getJSClassName());
    }

    public function drag($categories, $orders) {
        if (empty($categories) || !is_array($categories) || count($categories)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($categories)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_categories = array();
        $_orders = array();
        foreach($categories as $id) {
            $_category = new Forum\Models\Category($id);
            if (!$_category->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_categories []= $_category;
            $_orders []= $_category->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_categories as $index=>$category) {
            $category->sort_order = intval($orders[$index]);
            $category->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}