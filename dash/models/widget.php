<?php
/**
 * Zira project.
 * widget.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Widget extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Dash\Forms\Widget();
        if ($form->isValid()) {
            $id = $form->getValue('id');
            if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));

            $available_widgets = Dash\Windows\Widgets::getAvailableWidgets();

            if (is_numeric($id)) {
                $widget = new Zira\Models\Widget($id);
                if (!$widget->loaded() || !array_key_exists($widget->name, $available_widgets)) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }
                if (!$available_widgets[$widget->name]->isEditable()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }
            } else {
                if (!array_key_exists($id, $available_widgets)) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }
                if (!$available_widgets[$id]->isEditable()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }

                $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

                $widget = new Zira\Models\Widget();
                $widget->name = $id;
                $widget->module = strtolower(substr($id, 1, strpos($id, '\\', 1)-1));
                $widget->params = null;
                $widget->sort_order = ++$max_order;
            }

            $language = $form->getValue('language');
            if (empty($language)) $language = null;
            $widget->language = $language;
            $category_id = intval($form->getValue('category_id'));
            if ($category_id<0) $category_id = null;
            else if ($category_id==0) $category_id = Zira\Category::ROOT_CATEGORY_ID;
            $widget->category_id = $category_id;
            $widget->placeholder = $form->getValue('placeholder');
            $widget->active = intval($form->getValue('active')) ? Zira\Models\Widget::STATUS_ACTIVE : Zira\Models\Widget::STATUS_NOT_ACTIVE;
            $widget->filter = $form->getValue('filter') ? $form->getValue('filter') : null;
            $widget->save();

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}