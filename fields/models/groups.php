<?php
/**
 * Zira project.
 * groups.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Dash;
use Fields;
use Zira\Permission;

class Groups extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Fields\Forms\Group();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $group = new Fields\Models\Group($id);
                if (!$group->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = Fields\Models\Group::getCollection()->max('sort_order')->get('mx');
                $group = new Fields\Models\Group();
                $group->sort_order = ++$max_order;
            }
            $group->title = $form->getValue('title');
            $group->description = $form->getValue('description');
            $group->placeholder = $form->getValue('placeholder');
            $group->category_id = (int)$form->getValue('category_id');
            $language = $form->getValue('language');
            if (empty($language)) $language = null;
            $group->language = $language;
            $group->active = (int)$form->getValue('active') ? 1 : 0;
            
            $group->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $group_id) {
            $group = new Fields\Models\Group($group_id);
            if (!$group->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            
            // deleting non-empty group is not allowed
            $co = Fields\Models\Field::getCollection()
                                ->count()
                                ->where('field_group_id','=',$group_id)
                                ->get('co');
            
            if ($co>0) return array('error' => Zira\Locale::tm('Cannot delete group that have fields', 'fields'));
            
            $group->delete();

            /**
            Fields\Models\Field::getCollection()
                                ->where('field_group_id','=',$group_id)
                                ->delete()
                                ->execute();
            
            Fields\Models\Value::getCollection()
                                ->where('field_group_id','=',$group_id)
                                ->delete()
                                ->execute();
             */
        }

        return array('reload' => $this->getJSClassName());
    }
    
    public function drag($items, $orders) {
        if (empty($items) || !is_array($items) || count($items)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($items)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_items = array();
        $_orders = array();
        foreach($items as $id) {
            $_item = new Fields\Models\Group($id);
            if (!$_item->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_items []= $_item;
            $_orders []= $_item->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_items as $index=>$item) {
            $item->sort_order = intval($orders[$index]);
            $item->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}