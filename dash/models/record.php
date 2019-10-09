<?php
/**
 * Zira project.
 * record.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Record extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Dash\Forms\Record();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ((empty($id) && !Permission::check(Permission::TO_CREATE_RECORDS)) ||
                (!empty($id) && !Permission::check(Permission::TO_EDIT_RECORDS))
            ) {
                return array('error'=>Zira\Locale::t('Permission denied'));
            }

            $publish_state = null;
            if (!empty($id)) {
                $record = new Zira\Models\Record($id);
                if (!$record->loaded()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                }

                $publish_state = $record->published;
                
                if ($form->getValue('delete_image')) {
                    $record->image = null;
                    if ($record->thumb) {
                        $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $record->thumb);
                        if (file_exists($thumb)) {
                            @unlink($thumb);
                        }
                        $record->thumb = null;
                    }
                }
            } else {
                $record = new Zira\Models\Record();
                $record->author_id = Zira\User::getCurrent()->id;
                $record->creation_date = date('Y-m-d H:i:s');
            }

            $record->category_id = (int)$form->getValue('category_id');
            $record->name = $form->getValue('name');
            $record->title = $form->getValue('title');
            $record->language = $form->getValue('language');
            $record->access_check = (int)$form->getValue('access_check');
            $record->gallery_check = (int)$form->getValue('gallery_check');
            $record->files_check = (int)$form->getValue('files_check');
            $record->audio_check = (int)$form->getValue('audio_check');
            $record->video_check = (int)$form->getValue('video_check');
            $record->published = (bool)$form->getValue('published') ? Zira\Models\Record::STATUS_PUBLISHED : Zira\Models\Record::STATUS_NOT_PUBLISHED;
            $record->front_page = (bool)$form->getValue('front_page') ? Zira\Models\Record::STATUS_FRONT_PAGE : Zira\Models\Record::STATUS_NOT_FRONT_PAGE;
            $record->comments_enabled = (int)$form->getValue('comments_enabled');
            $record->modified_date = date('Y-m-d H:i:s');

            $record->save();
            Zira\Models\Search::indexRecord($record);
            
            if (empty($id)) {
                // running create hook
                Records::runRecordCreateHook($record);
            } else if ($publish_state != $record->published) {
                if ($record->published == Zira\Models\Record::STATUS_PUBLISHED) {
                    // running publish hook
                    Records::runRecordPublishHook($record);
                } else {
                    // running unpublish hook
                    Records::runRecordUnpublishHook($record);
                }
            }

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}