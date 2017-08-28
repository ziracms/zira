<?php
/**
 * Zira project.
 * recordaudio.php
 * (c)2017 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordaudio extends Model {
    public function addRecordAudio($id, $files, $url=null, $code=null) {
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

                $audioObj = new Zira\Models\Audio();
                $audioObj->record_id = $record->id;
                $audioObj->path = str_replace(DIRECTORY_SEPARATOR, '/', $file);
                //$audioObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
                $audioObj->save();
            }
        } else if (!empty($url)) {
            $audioObj = new Zira\Models\Audio();
            $audioObj->record_id = $record->id;
            $audioObj->url = $url;
            //$audioObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $audioObj->save();
        } else if (!empty($code)) {
            $audioObj = new Zira\Models\Audio();
            $audioObj->record_id = $record->id;
            $audioObj->embed = $code;
            //$audioObj->description = Zira\Helper::utf8Clean(strip_tags($record->title));
            $audioObj->save();
        }
        
        $audio_count = Zira\Page::getRecordAudioCount($record->id);
        $record->audio_count = intval($audio_count);
        $record->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function editRecordAudio($id, $url=null, $code=null) {
        if (empty($id) || (empty($url) && empty($code))) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        
        $audioObj = new Zira\Models\Audio($id);
        if (!$audioObj->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!empty($url)) {
            $audioObj->url = $url;
        } else if (!empty($code)) {
            $audioObj->embed = $code; 
        }
        
        $audioObj->save();
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function saveDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) || !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $audio = new Zira\Models\Audio($id);
        if (!$audio->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $audio->description = Zira\Helper::utf8Clean(strip_tags($description));
        $audio->save();

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
            $audio = new Zira\Models\Audio($id);
            if (!$audio->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            $audio->delete();
            
            $record_ids []= $audio->record_id;
        }
        
        foreach($record_ids as $record_id) {
            $record = new Zira\Models\Record($record_id);
            if (!$record->loaded()) continue;
            $audio_count = Zira\Page::getRecordAudioCount($record->id);
            $record->audio_count = intval($audio_count);
            $record->save();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
}