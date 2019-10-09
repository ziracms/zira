<?php
/**
 * Zira project.
 * recordfiles.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordfiles extends Model {
    public function addRecordFiles($id, $files, $url) {
        if (empty($id) || (is_array($files) && empty($files)) || (!is_array($files) && empty($url))) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (is_array($files)) {
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
        } else if (!empty($url)) {
            $fileObj = new Zira\Models\File();
            $fileObj->record_id = $record->id;
            $fileObj->url = $url;
            //$fileObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $fileObj->save();
        }
        
        $files_count = Zira\Page::getRecordFilesCount($record->id);
        $record->files_count = intval($files_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function addFolderFiles($id, $folder) {
        if (empty($id) || empty($folder)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $folder = trim((string)$folder,DIRECTORY_SEPARATOR);
        if (strpos($folder,'..')!==false || strpos($folder,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if ($folder==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $folder;
        if (!file_exists($path)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!is_dir($path)) return array('error' => Zira\Locale::t('An error occurred'));

        $d = opendir($path);
        if (!$d) return array('error' => Zira\Locale::t('An error occurred'));
        
        while(($f = readdir($d))!==false) {
            if ($f == '.' || $f == '..') continue;
            if (is_dir($path . DIRECTORY_SEPARATOR . $f)) continue;
            $file = $folder . DIRECTORY_SEPARATOR . $f;
            if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $file)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            $fileObj = new Zira\Models\File();
            $fileObj->record_id = $record->id;
            $fileObj->path = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            //$fileObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $fileObj->save();
        }
        
        closedir($d);
        
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