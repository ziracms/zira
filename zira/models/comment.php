<?php
/**
 * Zira project.
 * comment.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira;
use Zira\Config;
use Zira\Helper;
use Zira\Locale;
use Zira\Orm;

class Comment extends Orm {
    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;
    const PATH_DELIMITER = '.';

    public static $table = 'comments';
    public static $pk = 'id';
    public static $alias = 'cmt';

    public static function getFields() {
        return array(
            'id',
            'record_id',
            'author_id',
            'parent_id',
            'sort_path',
            'path_offset',
            'content',
            'sender_name',
            'recipient_name',
            'likes',
            'dislikes',
            'creation_date',
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
            Record::getClass() => 'record_id',
            User::getClass() => 'author_id'
        );
    }

    public static function countComments($record_id, $published= true) {
        $query = self::getCollection()
                ->count()
                ->where('record_id','=',$record_id);
        if ($published) {
            $query->and_where('published', '=', self::STATUS_PUBLISHED);
        }
        return $query->get('co');
    }

    public static function getComments($record_id, $limit = null, $offset = 0, $published= true) {
        if ($limit===null) $limit = Config::get('comments_limit', 10);

        $query = self::getCollection()
                    ->select(self::getFields())
                    ->left_join(User::getClass(), array('author_username'=>'username','author_firstname'=>'firstname','author_secondname'=>'secondname','author_image'=>'image'))
                    ->where('record_id','=',$record_id);
        if ($published) {
            $query->and_where('published', '=', self::STATUS_PUBLISHED);
        }
        $query->order_by('sort_path','asc');
        $query->limit($limit, $offset);
        return $query->get();
    }

    public static function getPathOffset($record_id, $parent = null) {
        $parent_id = 0;
        if ($parent!==null) {
            $parent_id = $parent->id;
            $parts = explode(self::PATH_DELIMITER, $parent->sort_path);
            if (count($parts)>=50) return $parent->path_offset-1;
        }

        $offset= self::getCollection()
                        ->max('path_offset')
                        ->where('record_id','=',$record_id)
                        ->and_where('parent_id','=',$parent_id)
                        ->get('mx');

        return intval($offset);
    }

    public static function getSortPath($path_offset, $parent = null) {
        if ($parent===null) {
            $path = self::generatePath($path_offset, true);
        } else {
            $parts = explode(self::PATH_DELIMITER, $parent->sort_path);
            if (count($parts)>=50) $path = $parent->sort_path;
            else $path = $parent->sort_path . self::PATH_DELIMITER . self::generatePath($path_offset, false);
        }

        return $path;
    }

    public static function generatePath($offset, $desc = false) {
        if ($desc) $chars = range('z','a');
        else $chars = range('a','z');
        $count = count($chars);
        $max = pow($count, 4);
        if ($offset >= $max) $offset = $max - 1;
        $offset1 = intval($offset / pow($count,3));
        $offset2 = intval(($offset % pow($count,3)) / pow($count,2)) % $count;
        $offset3 = intval(($offset % pow($count,2)) / $count) % $count;
        $offset4 = $offset % $count;
        $path = '';
        if ($offset1<$count) $path .= $chars[$offset1];
        if ($offset2<$count) $path .= $chars[$offset2];
        if ($offset3<$count) $path .= $chars[$offset3];
        if ($offset4<$count) $path .= $chars[$offset4];
        return $path;
    }

    public static function getDefaultNotifyMessage() {
        $message = Locale::t('Hello %s !', Locale::t('moderator'))."\r\n\r\n";
        $message .= Locale::t('New comment was posted on: %s', '$page')."\r\n";
        $message .= Locale::t('Page URL address: %s','$url')."\r\n\r\n";
        $message .= Locale::t('Comment text').':'."\r\n";
        $message .= '$comment'."\r\n\r\n";
        $message .= Locale::t('You recieved this message, because your Email address is specified as a notification Email on %s','$site');
        return $message;
    }

    public static function notify($record, $comment) {
        $email = Config::get('comment_notify_email');
        if (empty($email)) return;
        if (Zira\User::isAuthorized() && Zira\User::getCurrent()->email == $email) return;

        if ($record->category_id != Zira\Category::ROOT_CATEGORY_ID) {
            $category = new Zira\Models\Category($record->category_id);
            if (!$category->loaded()) return;
            $url = Zira\Page::generateRecordUrl($category->name, $record->name);
        } else {
            $url = Zira\Page::generateRecordUrl(null, $record->name);
        }

        $message = Config::get('comment_notification_message');
        if (!$message || strlen(trim($message))==0) {
            $message = self::getDefaultNotifyMessage();
        } else {
            $message = Locale::t($message);
        }
        $message = str_replace('$page', $record->title, $message);
        $message = str_replace('$url', Helper::url($url, true, true), $message);
        $message = str_replace('$comment', $comment->content, $message);
        $message = str_replace('$site', Helper::url('/',true, true), $message);

        Zira\Mail::send($email, Locale::t('New comment was posted'), Helper::html($message));
    }
    
    public static function removeRecordComments($record_id) {
        self::getCollection()
                ->delete()
                ->where('record_id', '=', $record_id)
                ->execute();
    }
}