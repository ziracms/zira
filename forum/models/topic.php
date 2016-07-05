<?php
/**
 * Zira project.
 * topic.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Topic extends Orm {
    public static $table = 'forum_topics';
    public static $pk = 'id';
    public static $alias = 'frm_tpc';

    const STATUS_NONE = 0;
    const STATUS_SOLVED = 1;
    const STATUS_DENIED = 2;

    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;

    public static function getFields() {
        return array(
            'id',
            'category_id',
            'forum_id',
            'creator_id',
            'title',
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'info',
            'date_created',
            'date_modified',
            'messages',
            'last_user_id',
            'active',
            'status',
            'sticky',
            'published'
        );
    }

    public static function getTable() {
        return self::$table;
    }

    public static function getPk() {
        return self::$pk;
    }

    public static function getAlias() {
        return self::$alias;
    }

    public static function getReferences() {
        return array(
            Category::getClass() => 'category_id',
            Forum::getClass() => 'forum_id',
            Zira\Models\User::getClass() => 'last_user_id'
        );
    }

    public static function generateUrl($topic) {
        $id = is_numeric($topic) ? intval($topic) : $topic->id;
        return \Forum\Forum::ROUTE . '/thread/' . $id;
    }

    public static function getStatuses() {
        return array(
            self::STATUS_NONE => Zira\Locale::tm('Default', 'forum'),
            self::STATUS_SOLVED => Zira\Locale::tm('Solved', 'forum'),
            self::STATUS_DENIED => Zira\Locale::tm('Denied', 'forum')
        );
    }

    public static function getStatus($status) {
        $statuses = self::getStatuses();
        if (!array_key_exists($status, $statuses)) return '';
        return $statuses[$status];
    }

    public static function createNewTopic($category_id, $forum_id, $title, $content, $forum_topic_co, &$message = null) {
        if (!Zira\User::isAuthorized()) return false;

        $topic = new self();
        $topic->category_id = $category_id;
        $topic->forum_id = $forum_id;
        $topic->creator_id = Zira\User::getCurrent()->id;
        $topic->title = $title;
        $topic->date_created = date('Y-m-d H:i:s');
        $topic->date_modified = date('Y-m-d H:i:s');

        if (Zira\Permission::check(\Forum\Forum::PERMISSION_MODERATE) ||
            !Zira\Config::get('forum_moderate', false)
        ) {
            $topic->published = self::STATUS_PUBLISHED;
        } else {
            $topic->published = self::STATUS_NOT_PUBLISHED;
        }

        try {
            $topic->save();
        } catch(\Exception $err) {
            return false;
        }

        if (!($message=Message::createNewMessage($forum_id, $topic->id, $content, 1, $forum_topic_co))) return false;

        if ($topic->published == self::STATUS_PUBLISHED) {
            \Forum\Models\Search::indexTopic($topic);
        }

        return $topic;
    }

    public static function deleteTopic($topic_id) {
        $topic = new self($topic_id);
        if (!$topic->loaded()) return false;

        $topic->delete();

        // deleting files
        $messages = Message::getCollection()
                    ->where('topic_id','=',$topic->id)
                    ->get();
        foreach($messages as $message) {
            File::deleteFiles($message->id);
        }

        if ($topic->published == self::STATUS_PUBLISHED) {
            // decreasing user posts count
            $messages = Message::getCollection()
                ->count()
                ->select(array('creator_id'))
                ->where('topic_id', '=', $topic->id)
                ->and_where('published','=',\Forum\Models\Message::STATUS_PUBLISHED)
                ->group_by('creator_id')
                ->get();

            if ($messages) {
                foreach ($messages as $message) {
                    $user = new Zira\Models\User($message->creator_id);
                    if (!$user->loaded()) continue;
                    $user->posts -= $message->co;
                    if ($user->posts < 0) $user->posts = 0;
                    $user->save();
                }
            }

            // decreasing forum's topics count
            $forum = new Forum($topic->forum_id);
            if ($forum->loaded()) {
                if ($forum->last_user_id == $topic->creator_id) {
                    $forum->last_user_id = null;
                }
                $forum->topics--;
                if ($forum->topics < 0) $forum->topics = 0;
                $forum->save();
            }
        }

        // deleting topic messages
        Message::getCollection()
                        ->delete()
                        ->where('topic_id','=',$topic->id)
                        ->execute();

        // removing from search
        \Forum\Models\Search::clearTopicIndex($topic);

        return true;
    }
}