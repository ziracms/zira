<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Message extends Orm {
    public static $table = 'forum_messages';
    public static $pk = 'id';
    public static $alias = 'frm_msg';

    const STATUS_NONE = 0;
    const STATUS_INFO = 1;
    const STATUS_MESSAGE = 2;
    const STATUS_WARNING = 3;

    public static function getFields() {
        return array(
            'id',
            'topic_id',
            'creator_id',
            'content',
            'date_created',
            'date_modified',
            'modified_by',
            'rating',
            'status'
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
            Topic::getClass() => 'topic_id',
            Zira\Models\User::getClass() => 'creator_id'
        );
    }

    public static function getStatuses() {
        return array(
            self::STATUS_NONE => Zira\Locale::tm('Default', 'forum'),
            self::STATUS_INFO => Zira\Locale::tm('Information', 'forum'),
            self::STATUS_MESSAGE => Zira\Locale::tm('Message', 'forum'),
            self::STATUS_WARNING => Zira\Locale::tm('Warning', 'forum')
        );
    }

    public static function createNewMessage($forum_id, $topic_id, $content, $topic_messages_co, $forum_topics_co, $status=null) {
        if (!Zira\User::isAuthorized()) return false;

        $message = new self();
        $message->topic_id = $topic_id;
        $message->creator_id = Zira\User::getCurrent()->id;
        $message->content = Zira\Helper::utf8Entity(html_entity_decode($content));
        $message->date_created = date('Y-m-d H:i:s');
        $message->date_modified = date('Y-m-d H:i:s');
        if ($status!==null) $message->status = $status;

        try {
            $message->save();
        } catch(\Exception $err) {
            return false;
        }

        Topic::getCollection()
                ->update(array(
                    'date_modified' => date('Y-m-d H:i:s'),
                    'last_user_id' => Zira\User::getCurrent()->id,
                    'messages' => $topic_messages_co
                ))->where('id','=',$topic_id)
                ->execute();

        Forum::getCollection()
                ->update(array(
                    'date_modified' => date('Y-m-d H:i:s'),
                    'last_user_id' => Zira\User::getCurrent()->id,
                    'topics' => $forum_topics_co
                ))->where('id','=',$forum_id)
                ->execute();

        Zira\User::getCurrent()->posts++;
        Zira\User::getCurrent()->save();

        return $message;
    }

    public static function deleteMessage($message_id) {
        $message = new self($message_id);
        if (!$message->loaded()) return false;

        $message->delete();

        $user = new Zira\Models\User($message->creator_id);
        if ($user->loaded()) {
            $user->posts--;
            $user->save();
        }

        $topic = new Topic($message->topic_id);
        if ($topic->loaded()) {
            if ($topic->last_user_id == $message->creator_id) {
                $topic->last_user_id = null;
            }
            $topic->messages--;
            if ($topic->messages<0) $topic->messages = 0;
            $topic->save();
        }

        if ($topic->loaded()) {
            $forum = new Forum($topic->forum_id);
            if ($forum->loaded() && $forum->last_user_id == $message->creator_id) {
                $forum->last_user_id = null;
                $forum->save();
            }
        }

        File::deleteFiles($message->id);

        return true;
    }
}