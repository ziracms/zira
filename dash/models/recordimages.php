<?php
/**
 * Zira project.
 * recordimages.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordimages extends Model {
    public function addRecordImages($id, $images) {
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

            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $record->category_id, $record->id, true);
            if (!$thumb) continue;

            $imageObj = new Zira\Models\Image();
            $imageObj->record_id = $record->id;
            $imageObj->thumb = $thumb;
            $imageObj->image = str_replace(DIRECTORY_SEPARATOR, '/', $image);
            $imageObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $imageObj->save();
        }
        
        $images_count = Zira\Page::getRecordImagesCount($record->id);
        $record->images_count = intval($images_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function saveDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $image = new Zira\Models\Image($id);
        if (!$image->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $image->description = Zira\Helper::utf8Clean(strip_tags($description));
        $image->save();

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
            $image = new Zira\Models\Image($id);
            if (!$image->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $image->delete();

            $record_ids []= $image->record_id;
            
            if ($image->thumb) {
                $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image->thumb);
                if (file_exists($thumb)) @unlink($thumb);
            }
        }
        
        foreach($record_ids as $record_id) {
            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) continue;
            $images_count = Zira\Page::getRecordImagesCount($record->id);
            $record->images_count = intval($images_count);
            $record->save();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}