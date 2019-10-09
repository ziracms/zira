<?php
/**
 * Zira project.
 * recordtext.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Recordtext extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        if (!isset($data['item']) || !isset($data['content'])) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $item = $data['item'];
        $content = $data['content'];

        $record = new Zira\Models\Record($item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        if (!$record->content && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        if (($p=strpos($content, '<!-- pagebreak -->'))!==false) {
            $record->description = strip_tags(substr($content, 0, $p));
            $record->description = str_replace('.','. ',$record->description);
        } else if (!$record->description) {
            $_content = strip_tags(html_entity_decode($content));
            if (mb_strlen($_content, CHARSET)>100) {
                $record->description = mb_substr($_content, 0, 100, CHARSET).'...';
            } else {
                $record->description = $_content;
            }
        }

        if (!$record->thumb && Zira\Config::get('create_thumbnails', 1) && (preg_match('/<img[\x20][^>]*?src[\x20]*[=][\x20]*(?:\'|")([^\'"]+)/',$content, $m))) {
            if (strpos($m[1], BASE_URL) === 0) $m[1] = substr($m[1], strlen(BASE_URL));
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, urldecode($m[1]));
            $thumb = Zira\Page::createRecordThumb($path, $record->category_id, $record->id);
            if ($thumb) {
                $record->thumb = $thumb;
            }
        }

        $record->description = Zira\Helper::utf8Clean($record->description);
        $record->content = Zira\Helper::utf8Entity($content);
        $record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        Zira\Models\Draft::getCollection()
                ->update(array(
                    'published' => Zira\Models\Draft::STATUS_PUBLISHED
                ))->where('record_id','=',$record->id)
                ->execute();

        return array('message' => Zira\Locale::t('Successfully saved'));
    }

    public function loadDraft($id) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array();
        }
        if (empty($id)) {
            return array();
        }

        $draft = new Zira\Models\Draft($id);

        if (!$draft->loaded() || $draft->author_id != Zira\User::getCurrent()->id) {
            return array();
        }

        return array('draft'=>$draft->content);
    }

    public function saveDraft($id, $content) {
        if (empty($content)) return array();
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array();
        }
        if (empty($id) || empty($content)) {
            return array();
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array();
        }

        $draft = new Zira\Models\Draft();

        $row = Zira\Models\Draft::getCollection()
                            ->where('record_id','=',$record->id)
                            ->and_where('author_id','=',Zira\User::getCurrent()->id)
                            ->get(0, true);

        if ($row) {
            $draft->loadFromArray($row);
        } else {
            $draft->record_id = $record->id;
            $draft->author_id = Zira\User::getCurrent()->id;
            $draft->creation_date = date('Y-m-d H:i:s');
        }

        $draft->content = Zira\Helper::utf8Entity($content);
        $draft->modified_date = date('Y-m-d H:i:s');
        $draft->published = Zira\Models\Draft::STATUS_NOT_PUBLISHED;
        $draft->save();

        return array('success'=>1);
    }
}