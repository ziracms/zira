<?php
/**
 * Zira project.
 * topics.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Topics extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Forum\Forms\Topic();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            if ($id) {
                $thread = new Forum\Models\Topic($id);
                if (!$thread->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

                if ($thread->forum_id != $form->getValue('forum_id')) {
                    $forum_old = new \Forum\Models\Forum($thread->forum_id);
                    if (!$forum_old->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
                    $forum_new = new \Forum\Models\Forum($form->getValue('forum_id'));
                    if (!$forum_new->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

                    $forum_old->topics--;
                    if ($forum_old->topics < 0) $forum_old->topics = 0;
                    $forum_old->save();

                    $forum_new->topics++;
                    $forum_new->save();

                    $thread->forum_id = (int)$form->getValue('forum_id');
                }
            } else {
                $forum = new \Forum\Models\Forum($form->getValue('forum_id'));
                if (!$forum->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

                $forum->topics++;
                $forum->save();

                $thread = new Forum\Models\Topic();
                $thread->category_id = $forum->category_id;
                $thread->forum_id = $forum->id;
                $thread->creator_id = Zira\User::getCurrent()->id;
                $thread->date_created = date('Y-m-d H:i:s');
            }

            $thread->title = $form->getValue('title');
            $description = $form->getValue('description');
            $thread->description = !empty($description) ? $description : null;
            $meta_title = $form->getValue('meta_title');
            $thread->meta_title = !empty($meta_title) ? $meta_title : null;
            $meta_description = $form->getValue('meta_description');
            $thread->meta_description = !empty($meta_description) ? $meta_description : null;
            $meta_keywords = $form->getValue('meta_keywords');
            $thread->meta_keywords = !empty($meta_keywords) ? $meta_keywords : null;
            $info = $form->getValue('info');
            $thread->info = !empty($info) ? $info : null;
            $thread->status = (int)$form->getValue('status');
            $thread->active = (int)$form->getValue('active') ? 1 : 0;
            $thread->sticky = (int)$form->getValue('sticky') ? 1 : 0;
            $thread->date_modified = date('Y-m-d H:i:s');

            $thread->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $topic_id) {
            Forum\Models\Topic::deleteTopic($topic_id);
        }

        return array('reload' => $this->getJSClassName());
    }
}