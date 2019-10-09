<?php
/**
 * Zira project.
 * search.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class Search extends Orm {
    public static $table = 'forum_search';
    public static $pk = 'id';
    public static $alias = 'frm_srch';

    const MIN_CHARS = 3;

    public static function getFields() {
        return array(
            'id',
            'category_id',
            'forum_id',
            'topic_id',
            'keyword'
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
            Topic::getClass() => 'topic_id'
        );
    }

    public static function clearTopicIndex($topic) {
        self::getCollection()
            ->delete()
            ->where('category_id', '=', $topic->category_id)
            ->and_where('forum_id', '=', $topic->forum_id)
            ->and_where('topic_id', '=', $topic->id)
            ->execute();
    }

    public static function indexTopic($topic) {
        self::clearTopicIndex($topic);

        if ($topic->published != \Forum\Models\Topic::STATUS_PUBLISHED) return;

        $keywords_str = '';
        if ($topic->meta_keywords) {
            $keywords = explode(',', $topic->meta_keywords);
            foreach ($keywords as $keyword) {
                $keywords_str .= ' ' . $keyword;
            }
        }

        $text = $topic->title . ' ' . $topic->meta_title . ' ' . $keywords_str;
        $text = trim($text);
        if (empty($text)) return;
        $text = mb_strtolower($text, CHARSET);
        $text = preg_replace('/[\x20]+/',' ', $text);
        $keywords = explode(' ', $text);

        $added = array();
        foreach($keywords as $keyword) {
            if (in_array($keyword, $added)) continue;
            if (mb_strlen($keyword, CHARSET)<self::MIN_CHARS) continue;
            $index = new self();
            $index->keyword = $keyword;
            $index->category_id = $topic->category_id;
            $index->forum_id = $topic->forum_id;
            $index->topic_id = $topic->id;
            $index->save();
            $added []= $keyword;
        }
    }

    public static function getTopics($text, $limit = 10, $offset = 0, $category_id=0, $forum_id=0) {
        $text = trim($text);
        if (empty($text)) return array();
        $text = mb_strtolower($text, CHARSET);
        $keywords = explode(' ', $text);

        $query = self::getCollection();

        $added = array();
        foreach($keywords as $index=>$keyword) {
            if (mb_strlen($keyword, CHARSET)<self::MIN_CHARS) continue;
            if (in_array($keyword, $added)) continue;
            if (count($added)>0) $query->union();
            $query->open_query();
            $query->select('topic_id');
            $query->where('keyword','like',$keyword.'%');
            if ($category_id>0) {
                $query->and_where('category_id', '=', $category_id);
            }
            if ($category_id>0 && $forum_id>0) {
                $query->and_where('forum_id', '=', $forum_id);
            }
            $query->limit($limit+$offset);
            $query->close_query();
            $added []= $keyword;
            if (count($added)>=5) break;
        }

        if (empty($added)) return array();

        $query->merge();
        $query->limit($limit, $offset);
        $rows = $query->get();

        $results = array();
        foreach($rows as $row) {
            $results[]=$row->topic_id;
        }

        if (empty($results)) return array();

//        return \Forum\Models\Topic::getCollection()
//                            ->select(\Forum\Models\Topic::getFields())
//                            ->left_join(Zira\Models\User::getClass(), array('user_firstname'=>'firstname', 'user_secondname'=>'secondname', 'user_username'=>'username'))
//                            ->where('id','in',$results)
//                            ->get();

        $query = \Forum\Models\Topic::getCollection();
        foreach($results as $index=>$result) {
            if ($index>0) $query->union();
            $query->select(\Forum\Models\Topic::getFields())
                        ->left_join(Zira\Models\User::getClass(), array('user_firstname' => 'firstname', 'user_secondname' => 'secondname', 'user_username' => 'username'))
                        ->where('id', '=', $result)
                        ->get();
        }
        return $query->get();
    }
}