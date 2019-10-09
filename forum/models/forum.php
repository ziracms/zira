<?php
/**
 * Zira project.
 * forum.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Forum extends Orm {
    public static $table = 'forum_forums';
    public static $pk = 'id';
    public static $alias = 'frm_frm';

    public static function getFields() {
        return array(
            'id',
            'category_id',
            'title',
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'access_check',
            'info',
            'date_created',
            'date_modified',
            'topics',
            'last_user_id',
            'sort_order',
            'active',
            'language'
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
            Zira\Models\User::getClass() => 'last_user_id'
        );
    }

    public static function generateUrl($forum) {
        $id = is_numeric($forum) ? intval($forum) : $forum->id;
        return \Forum\Forum::ROUTE . '/threads/' . $id;
    }

    public static function getArray($category_id) {
        $forums = self::getCollection()
                        ->where('category_id', '=', $category_id)
                        ->get();

        $forums_arr = array();
        foreach($forums as $forum) {
            $forums_arr[$forum->id] = $forum->title;
        }

        return $forums_arr;
    }
}