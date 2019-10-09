<?php
/**
 * Zira project.
 * recordvideos.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordvideos extends Model {
    public function addRecordVideos($id, $files, $url=null, $code=null) {
        if (empty($id) || (is_array($files) && empty($files)) || (!is_array($files) && empty($url) && empty($code))) return array('error' => Zira\Locale::t('An error occurred'));
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

                $videoObj = new Zira\Models\Video();
                $videoObj->record_id = $record->id;
                $videoObj->path = str_replace(DIRECTORY_SEPARATOR, '/', $file);
                //$videoObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
                $videoObj->save();
            }
        } else if (!empty($url)) {
            $videoObj = new Zira\Models\Video();
            $videoObj->record_id = $record->id;
            $videoObj->url = $url;
            //$videoObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $videoObj->save();
        } else if (!empty($code)) {
            $videoObj = new Zira\Models\Video();
            $videoObj->record_id = $record->id;
            $videoObj->embed = $code;
            //$videoObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $videoObj->save();
        }
        
        $videos_count = Zira\Page::getRecordVideosCount($record->id);
        $record->video_count = intval($videos_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function addFolderVideos($id, $folder) {
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

            $videoObj = new Zira\Models\Video();
            $videoObj->record_id = $record->id;
            $videoObj->path = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            //$videoObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $videoObj->save();
        }
        
        closedir($d);
        
        $videos_count = Zira\Page::getRecordVideosCount($record->id);
        $record->video_count = intval($videos_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function editRecordVideo($id, $url=null, $code=null) {
        if (empty($id) || (empty($url) && empty($code))) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $videoObj = new Zira\Models\Video($id);
        if (!$videoObj->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!empty($url)) {
            $videoObj->url = $url;
        } else if (!empty($code)) {
            $videoObj->embed = $code; 
        }
        
        $videoObj->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function saveDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $video = new Zira\Models\Video($id);
        if (!$video->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $video->description = Zira\Helper::utf8Clean(strip_tags($description));
        $video->save();

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
            $video = new Zira\Models\Video($id);
            if (!$video->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $video->delete();
            
            $record_ids []= $video->record_id;
        }
        
        foreach($record_ids as $record_id) {
            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) continue;
            $videos_count = Zira\Page::getRecordVideosCount($record->id);
            $record->video_count = intval($videos_count);
            $record->save();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}