<?php
/**
 * Zira project.
 * menuitem.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Menuitem extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Menuitem();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if (!empty($id)) {
                $menuItem = new Zira\Models\Menu($id);
                if (!$menuItem->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = Zira\Models\Menu::getCollection()->max('sort_order')->get('mx');

                $menuItem = new Zira\Models\Menu();
                $menuItem->menu_id = (int)$form->getValue('menu_id');
                $menuItem->parent_id = (int)$form->getValue('parent_id');
                $menuItem->sort_order = ++$max_order;
            }

            $menuItem->language = $form->getValue('language') ? $form->getValue('language') : null;
            $menuItem->url = $form->getValue('url');
            $menuItem->title = $form->getValue('title');
            $menuItem->class = $form->getValue('class');
            $menuItem->external = (int)$form->getValue('external')>0 ? 1 : 0;
            $menuItem->active = (int)$form->getValue('active')>0 ? Zira\Models\Menu::STATUS_ACTIVE : Zira\Models\Menu::STATUS_INACTIVE;

            $menuItem->save();

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}