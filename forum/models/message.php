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

    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;

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
            'status',
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

        if (Zira\Permission::check(\Forum\Forum::PERMISSION_MODERATE) ||
            !Zira\Config::get('forum_moderate', false)
        ) {
            $message->published = self::STATUS_PUBLISHED;
        } else {
            $message->published = self::STATUS_NOT_PUBLISHED;
        }

        try {
            $message->save();
        } catch(\Exception $err) {
            return false;
        }

        if ($message->published != self::STATUS_PUBLISHED) return $message;

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

        if ($message->published == self::STATUS_PUBLISHED) {
            // decreasing user posts count
            $user = new Zira\Models\User($message->creator_id);
            if ($user->loaded()) {
                $user->posts--;
                $user->save();
            }

            // decreasing topic's messages count
            $topic = new Topic($message->topic_id);
            if ($topic->loaded()) {
                if ($topic->last_user_id == $message->creator_id) {
                    $topic->last_user_id = null;
                }
                $topic->messages--;
                if ($topic->messages < 0) $topic->messages = 0;
                $topic->save();
            }

            // setting forum's last user id
            if ($topic->loaded()) {
                $forum = new Forum($topic->forum_id);
                if ($forum->loaded() && $forum->last_user_id == $message->creator_id) {
                    $forum->last_user_id = null;
                    $forum->save();
                }
            }
        }

        // deleting files
        File::deleteFiles($message->id);

        return true;
    }

    public static function getDefaultNotifyMessage() {
        $message = Zira\Locale::tm('Hello %s !', 'forum', Zira\Locale::t('moderator'))."\r\n\r\n";
        $message .= Zira\Locale::tm('New forum message was posted', 'forum')."\r\n\r\n";
        $message .= Zira\Locale::tm('Thread: %s', 'forum', '$thread')."\r\n";
        $message .= Zira\Locale::t('Message').':'."\r\n";
        $message .= '$message'."\r\n\r\n";
        $message .= Zira\Locale::t('You recieved this message, because your Email address is specified as a notification Email on %s','$site');
        return $message;
    }

    public static function notify($topic, $message) {
        $email = Zira\Config::get('forum_notify_email');
        if (empty($email)) return;
        if (Zira\User::isAuthorized() && Zira\User::getCurrent()->email == $email) return;

        //$url = \Forum\Models\Topic::generateUrl($topic);

//        $_message = Zira\Config::get('forum_notification_message');
//        if (!$_message || strlen(trim($_message))==0) {
//            $_message = self::getDefaultNotifyMessage();
//        } else {
//            $_message = Zira\Locale::t($_message);
//        }
        $_message = self::getDefaultNotifyMessage();

        $_message = str_replace('$thread', $topic->title, $_message);
        $_message = str_replace('$message', $message->content, $_message);
        $_message = str_replace('$site', Zira\Helper::url('/',true, true), $_message);

        Zira\Mail::send($email, Zira\Locale::tm('New forum message','forum'), Zira\Helper::html($_message));
    }

    public static function getNewMessagesCount() {
        return self::getCollection()
                                ->count()
                                ->where('published','=',self::STATUS_NOT_PUBLISHED)
                                ->get('co');
    }
}