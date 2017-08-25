<?php
/**
 * Zira project.
 * recordslides.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordslides extends Model {
    public function addRecordSlides($id, $images) {
        if (empty($id) || !is_array($images) || empty($images)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        foreach ($images as $image) {
            if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $image)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $record->category_id, $record->id, false, true);
            if (!$thumb) continue;

            $slideObj = new Zira\Models\Slide();
            $slideObj->record_id = $record->id;
            $slideObj->thumb = $thumb;
            $slideObj->image = str_replace(DIRECTORY_SEPARATOR, '/', $image);
            //$slideObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $slideObj->save();
        }
        
        $slides_count = Zira\Page::getRecordSlidesCount($record->id);
        $record->slides_count = intval($slides_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function saveDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $slide = new Zira\Models\Slide($id);
        if (!$slide->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $slide->description = Zira\Helper::utf8Clean(strip_tags($description));
        $slide->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message'=>Zira\Locale::t('Successfully saved'));
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record_ids = array();
        foreach($data as $id) {
            $slide = new Zira\Models\Slide($id);
            if (!$slide->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $slide->delete();
            
            $record_ids []= $slide->record_id;

            if ($slide->thumb) {
                $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $slide->thumb);
                if (file_exists($thumb)) @unlink($thumb);
            }
        }
        
        foreach($record_ids as $record_id) {
            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) continue;
            $slides_count = Zira\Page::getRecordSlidesCount($record->id);
            $record->slides_count = intval($slides_count);
            $record->save();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}