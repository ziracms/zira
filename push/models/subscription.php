<?php
/**
 * Zira project.
 * subscription.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Models;

use Zira;
use Zira\Orm;

class Subscription extends Orm {
    public static $table = 'push_subscriptions';
    public static $pk = 'id';
    public static $alias = 'psbr';

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

        );
    }

    public static function createSubscription($endpoint, $publicKey, $authToken, $contentEncoding) {
        if (empty($endpoint) || empty($publicKey) || empty($authToken) || empty($contentEncoding)) return;
        $user_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
        $anonymous_id = Zira\User::getAnonymousUserId();
        if (empty($user_id) && empty($anonymous_id)) return;
        if (Zira\User::isAuthorized()) {
            self::getCollection()
                        ->where('user_id', '=', $user_id)
                        ->delete()
                        ->execute();
        }
        self::getCollection()
                    ->where('anonymous_id', '=', $anonymous_id)
                    ->delete()
                    ->execute();
        $subscriptionArr = self::getCollection()
                                    ->where('endpoint', '=', $endpoint)
                                    ->get(0, true);
        $subscription = new self();
        if (!empty($subscriptionArr)) $subscription->loadFromArray($subscriptionArr);
        $subscription->user_id = $user_id;
        $subscription->anonymous_id = $anonymous_id;
        $subscription->endpoint = $endpoint;
        $subscription->pub_key = $publicKey;
        $subscription->auth_token = $authToken;
        $subscription->encoding = $contentEncoding;
        $subscription->language = Zira\Locale::getLanguage();
        $subscription->active = 1;
        $subscription->date_created = date('Y-m-d H:i:s');
        $subscription->save();
    }

    public static function deleteSubscription($endpoint) {
        if (empty($endpoint)) return;
        self::getCollection()
            ->where('endpoint', '=', $endpoint)
            ->delete()
            ->execute();
    }

    public static function disableSubscription($endpoint) {
        if (empty($endpoint)) return;
        self::getCollection()
            ->update(array('active' => 0))
            ->where('endpoint', '=', $endpoint)
            ->execute();
    }

    public static function isSubscriptionDisabled() {
        $subscription = self::getCollection()
                            ->where('anonymous_id', '=', Zira\User::getAnonymousUserId())
                            ->get(0);
        if (empty($subscription) && Zira\User::isAuthorized()) {
            $subscription = self::getCollection()
                                ->where('user_id', '=', Zira\User::getCurrent()->id)
                                ->get(0);
        }
        if (empty($subscription)) return false;
        return $subscription->active ? false : true;
    }
}