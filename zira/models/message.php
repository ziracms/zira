<?php
/**
 * Zira project.
 * message.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Models;

use Zira;
use Zira\Locale;
use Zira\Orm;

class Message extends Orm {
    const MIN_CHARS = 2;

    public static $table = 'messages';
    public static $pk = 'id';
    public static $alias = 'msg';

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
            Conversation::getClass() => 'conversation_id',
            User::getClass() => 'user_id'
        );
    }

    public static function getDefaultNotifyMessage() {
        $message = Locale::t('Hello %s !', '$user')."\r\n\r\n";
        $message .= Locale::t('You have new message from %s.', '$sender')."\r\n";
        $message .= Locale::t('Log in %s to read it.','$url')."\r\n\r\n";
        $message .= Locale::t('You recieved this message, because you are subscribed to Email notifications on %s','$site');
        return $message;
    }

    public static function notify($user, $sender) {
        if (!$user->subscribed || !$user->email || !$user->verified || !$user->active) return;

        $message = Zira\Config::get('new_message_notification');
        if (!$message || strlen(trim($message))==0) {
            $message = self::getDefaultNotifyMessage();
        } else {
            $message = Locale::t($message);
        }
        $message = str_replace('$user', Zira\User::getProfileName($user), $message);
        $message = str_replace('$sender', Zira\User::getProfileName($sender), $message);
        $message = str_replace('$url', Zira\Helper::url('user/login',true, true), $message);
        $message = str_replace('$site', Zira\Helper::url('/',true, true), $message);

        Zira\Mail::send($user->email, Locale::t('You have new message'), Zira\Helper::html($message));
    }
}