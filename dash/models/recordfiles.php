<?php
/**
 * Zira project.
 * recordfiles.php
 * (c)2017 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordfiles extends Model {
    public function addRecordFiles($id, $files) {
        if (empty($id) || !is_array($files) || empty($files)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        foreach ($files as $file) {
            if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $file)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            $fileObj = new Zira\Models\File();
            $fileObj->record_id = $record->id;
            $fileObj->path = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            //$fileObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $fileObj->save();
        }
        
        $files_count = Zira\Page::getRecordFilesCount($record->id);
        $record->files_count = intval($files_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function saveDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $file = new Zira\Models\File($id);
        if (!$file->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $file->description = Zira\Helper::utf8Clean(strip_tags($description));
        $file->save();

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
            $file = new Zira\Models\File($id);
            if (!$file->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $file->delete();
            
            $record_ids []= $file->record_id;
        }
        
        foreach($record_ids as $record_id) {
            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) continue;
            $files_count = Zira\Page::getRecordFilesCount($record->id);
            $record->files_count = intval($files_count);
            $record->save();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}