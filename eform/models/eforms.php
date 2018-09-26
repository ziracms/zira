<?php
/**
 * Zira project.
 * eforms.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Models;

use Zira;
use Dash;
use Eform;
use Zira\Permission;

class Eforms extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Eform\Forms\Eform();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $eform = new Eform\Models\Eform($id);
                if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $eform = new Eform\Models\Eform();
                $eform->creator_id = Zira\User::getCurrent()->id;
                $eform->date_created = date('Y-m-d H:i:s');
            }
            $eform->name = $form->getValue('name');
            $eform->email = $form->getValue('email');
            $eform->title = $form->getValue('title');
            $eform->description = $form->getValue('description');
            $eform->active = (int)$form->getValue('active') ? 1 : 0;

            $eform->save();

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

        foreach($data as $eform_id) {
            $eform = new Eform\Models\Eform($eform_id);
            if (!$eform->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $eform->delete();

            Eform\Models\Eformfield::getCollection()
                                ->where('eform_id','=',$eform_id)
                                ->delete()
                                ->execute();
        }

        return array('reload' => $this->getJSClassName());
    }

    public function info($id) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array();
        }

        $info = array();

        $eform = new Eform\Models\Eform($id);
        if (!$eform->loaded()) return array();

        $info[] = '<span class="glyphicon glyphicon-envelope"></span> ' . Zira\Helper::html($eform->email);
        $info[] = '<span class="glyphicon glyphicon-tag"></span> ' . Zira\Helper::html($eform->name);
        $info[] = '<span class="glyphicon glyphicon-paperclip"></span> ' . Zira\Helper::html($eform->title);
        $info[] = '<span class="glyphicon glyphicon-time"></span> ' . date(Zira\Config::get('date_format'), strtotime($eform->date_created));

        return $info;
    }
    
    public function createWidget($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::tm('Permission denied', 'dash'));
        }

        $eform = new Eform\Models\Eform(intval($id));
        if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Eform\Models\Eform::WIDGET_CLASS;
        $widget->module = 'eform';
        $widget->placeholder = Zira\View::VAR_CONTENT_BOTTOM;
        $widget->params = $eform->id;
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', 1));
    }
    
    public function createButton($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::tm('Permission denied', 'dash'));
        }

        $eform = new Eform\Models\Eform(intval($id));
        if (!$eform->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Eform\Models\Eform::BUTTON_CLASS;
        $widget->module = 'eform';
        $widget->placeholder = Zira\View::VAR_CONTENT_BOTTOM;
        $widget->params = $eform->id;
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', 1));
    }
}