<?php
/**
 * Zira project.
 * forums.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Forums extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Forum\Forms\Forum();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            if ($id) {
                $forum = new Forum\Models\Forum($id);
                if (!$forum->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = Forum\Models\Forum::getCollection()->max('sort_order')->get('mx');

                $forum = new Forum\Models\Forum();
                $forum->sort_order = ++$max_order;
                $forum->date_created = date('Y-m-d H:i:s');
                $forum->date_modified = date('Y-m-d H:i:s');
            }
            $forum->title = $form->getValue('title');
            $description = $form->getValue('description');
            $forum->description = !empty($description) ? $description : null;
            $meta_title = $form->getValue('meta_title');
            $forum->meta_title = !empty($meta_title) ? $meta_title : null;
            $meta_description = $form->getValue('meta_description');
            $forum->meta_description = !empty($meta_description) ? $meta_description : null;
            $meta_keywords = $form->getValue('meta_keywords');
            $forum->meta_keywords = !empty($meta_keywords) ? $meta_keywords : null;
            $info = $form->getValue('info');
            $forum->info = !empty($info) ? $info : null;
            $forum->category_id = (int)$form->getValue('category_id');
            $forum->access_check = (int)$form->getValue('access_check') ? 1 : 0;
            $forum->active = (int)$form->getValue('active') ? 1 : 0;

            $forum->save();

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

        $forums = array();
        foreach($data as $forum_id) {
            $forum = new Forum\Models\Forum($forum_id);
            if (!$forum->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };

            $co = Forum\Models\Topic::getCollection()
                                ->count()
                                ->where('forum_id', '=', $forum_id)
                                ->get('co');
            if ($co>0) return array('error' => Zira\Locale::tm('Forum "%s" has topics.','forum', $forum->title));

            $forums []= $forum;
        }

        foreach($forums as $forum) {
            $forum->delete();
        }

        return array('reload' => $this->getJSClassName());
    }

    public function drag($forums, $orders) {
        if (empty($forums) || !is_array($forums) || count($forums)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($forums)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_forums = array();
        $_orders = array();
        foreach($forums as $id) {
            $_forum = new Forum\Models\Forum($id);
            if (!$_forum->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_forums []= $_forum;
            $_orders []= $_forum->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_forums as $index=>$forum) {
            $forum->sort_order = intval($orders[$index]);
            $forum->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}