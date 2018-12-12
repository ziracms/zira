<?php
/**
 * Zira project.
 * holidays.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Models;

use Zira;
use Dash;
use Zira\Permission;

class Holidays extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Holiday\Forms\Holiday();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $holiday = new \Holiday\Models\Holiday($id);
                if (!$holiday->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $holiday = new \Holiday\Models\Holiday();
            }
            $holiday->cdate = $form->getValue('cdate');
            $holiday->title = $form->getValue('title');
            $holiday->description = $form->getValue('description');
            $holiday->image = $form->getValue('image');
            $holiday->audio = $form->getValue('audio');
            $holiday->cls = $form->getValue('cls');
            $holiday->active = (int)$form->getValue('active');

            $holiday->save();

            Zira\Cache::clear();

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

        foreach($data as $holiday_id) {
            $holiday = new \Holiday\Models\Holiday($holiday_id);
            if (!$holiday->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $holiday->delete();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function preview($id) {
        if (empty($id) || !is_numeric($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $holiday = new \Holiday\Models\Holiday($id);
        if (!$holiday->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $data = \Holiday\Holiday::celebrate($holiday->title, $holiday->description, $holiday->image, $holiday->audio, $holiday->cls, true);
        
        return array('data' => $data);
    }
}