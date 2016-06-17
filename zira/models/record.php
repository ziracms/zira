<?php
/**
 * Zira project.
 * record.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Record extends Orm {
    const STATUS_PUBLISHED = 1;
    const STATUS_NOT_PUBLISHED = 0;
    const STATUS_FRONT_PAGE = 1;
    const STATUS_NOT_FRONT_PAGE = 0;

    const REGEXP_NAME = '/^[a-zа-яё]+[a-zа-яё0-9\._-]*$/ui';

    public static $table = 'records';
    public static $pk = 'id';
    public static $alias = 'rcd';

    public static function getFields() {
        return array(
            'id',
            'category_id',
            'author_id',
            'name',
            'title',
            'description',
            'content',
            'thumb',
            'image',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'language',
            'access_check',
            'creation_date',
            'modified_date',
            'published',
            'front_page',
            'rating',
            'comments',
            'tpl'
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
            User::getClass() => 'author_id'
        );
    }

    public static function sortByRatingAsc($a, $b) {
        if ($a->rating == $b->rating) return 0;
        else return ($a->rating < $b->rating) ? -1 : 1;
    }

    public static function sortByRatingDesc($a, $b) {
        if ($a->rating == $b->rating) return 0;
        else return ($a->rating > $b->rating) ? -1 : 1;
    }

    public static function sortByCommentsAsc($a, $b) {
        if ($a->comments == $b->comments) return 0;
        else return ($a->comments < $b->comments) ? -1 : 1;
    }

    public static function sortByCommentsDesc($a, $b) {
        if ($a->comments == $b->comments) return 0;
        else return ($a->comments > $b->comments) ? -1 : 1;
    }
}