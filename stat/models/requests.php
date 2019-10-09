<?php
/**
 * Zira project.
 * requests.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Stat\Models;

use Zira;
use Dash;
use Stat;
use Zira\Permission;

class Requests extends Dash\Models\Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $access_id) {
            $access = new Stat\Models\Access($access_id);
            if (!$access->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $access->delete();
        }
        
        return array('reload' => $this->getJSClassName());
    }

    public function request($access_id) {
        $return = array();
        
        $access = new Stat\Models\Access($access_id);
        if ($access->loaded()) {
            if ($access->category_id>0) {
                $category = new Zira\Models\Category($access->category_id);
                if ($category->loaded()) {
                    $return []= Zira\Locale::t('Category').': '.Zira\Helper::html($category->title);
                }
            }
            if ($access->record_id>0) {
                $record = new Zira\Models\Record($access->record_id);
                if ($record->loaded()) {
                    $return []= Zira\Locale::t('Record').': '.Zira\Helper::html($record->title);
                }
            }
            $return []= Zira\Locale::t('URL').': '.Zira\Helper::html($access->url);
            $return []= Zira\Locale::t('IP').': '.Zira\Helper::html($access->ip);
            $return []= Zira\Locale::t('User-Agent').': '.Zira\Helper::html($access->ua);
            if ($access->referer) {
                $return []= Zira\Locale::t('Referer').': '.Zira\Helper::html($access->referer);
            }
            if (count(Zira\Config::get('languages'))>1) {
                $return []= Zira\Locale::t('Language').': '.Zira\Helper::html($access->language);
            }
            $mtime = strtotime($access->access_time);
            $date = date(Zira\Config::get('date_format'), $mtime);
            $time = date('H:i:s', $mtime);
            $return []= Zira\Locale::t('Time').': '.Zira\Helper::html($date.' - '.$time);
        }

        return $return;
    }
}