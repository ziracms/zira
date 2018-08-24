<?php
/**
 * Zira project.
 * comments.php
 * (c)2018 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Comments extends Zira\Widget {
    protected $_title = 'Last comments';

    protected function _init() {
        $this->setCaching(true);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_LEFT);
    }

    protected function getKey() {
       $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        $is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;

        return self::CACHE_PREFIX.'.'.strtolower(str_replace('\\','.',get_class($this))).'.side'.intval($is_sidebar).'.grid'.intval($is_grid).'.'.Zira\Locale::getLanguage();
    }

    public static function getLastCommentsRecordsList($limit = null, $last_id = null) {
        if ($limit === null) $limit = Zira\Config::get('widget_records_limit', 5);

        $query = Zira\Models\Comment::getCollection()
                    ->select(Zira\Models\Comment::getFields())
                    ->join(Zira\Models\Record::getClass())
                    ->left_join(Zira\Models\User::getClass(), array('author_username'=>'username','author_firstname'=>'firstname','author_secondname'=>'secondname','author_image'=>'image'))
                    ->where('published', '=', Zira\Models\Comment::STATUS_PUBLISHED)
                    ->and_where('published', '=', Zira\Models\Record::STATUS_PUBLISHED, Zira\Models\Record::getAlias())
                    ->and_where('language', '=', Zira\Locale::getLanguage(), Zira\Models\Record::getAlias())
                ;
        if ($last_id!==null) {
            $query->and_where('id', '<', $last_id);
        }
        $query->order_by('id', 'desc');
        $query->limit($limit);

        $comments = $query->get();

        if (!$comments) return array();

        $query = Zira\Models\Record::getCollection()
                        ->select('id', 'name','author_id','title','description','image','thumb','creation_date','rating','comments')
                        ->left_join(Zira\Models\Category::getClass(), array('category_name'=>'name', 'category_title'=>'title'))
                        ;

        $record_ids = array();
        foreach($comments as $index=>$row) {
            $record_ids []= $row->record_id;
        }
        $query->where('id','in',$record_ids);

        $_rows = $query->get();
        
        $records = array();
        foreach($_rows as $_row) {
            $records[$_row->id] = $_row;
        }
        
        $records_comments = array();
        foreach($comments as $comment) {
            if (array_key_exists($comment->record_id, $records)) {
                $record = $records[$comment->record_id];
                $item = new \stdClass();
                $item->id = $record->id;
                $item->name = $record->name;
                $item->author_id = $record->author_id;
                $item->title = $record->title;
                $item->description = $record->description;
                $item->thumb = $record->thumb;
                $item->creation_date = $record->creation_date;
                $item->rating = $record->rating;
                $item->comments = $record->comments;
                $item->category_name = $record->category_name;
                $item->category_title = $record->category_title;
                $item->comment_parent_id = $comment->parent_id;
                $item->comment_author_id = $comment->author_id;
                $item->comment_author_username = $comment->author_username;
                $item->comment_author_firstname = $comment->author_firstname;
                $item->comment_author_secondname = $comment->author_secondname;
                $item->comment_content = $comment->content;
                $item->comment_sender_name = $comment->sender_name;
                $item->comment_recipient_name = $comment->recipient_name;
                $item->comment_creation_date = $comment->creation_date;
                $records_comments []= $item;
            }
        }

        return $records_comments;
    }

    protected function _render() {
        $limit = Zira\Config::get('widget_records_limit', 5);

        $layout = Zira\Page::getLayout();
        if (!$layout) $layout = Zira\Config::get('layout');

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;
        //$is_grid = $layout && $layout != Zira\View::LAYOUT_ALL_SIDEBARS && !$is_sidebar;
        $is_grid = Zira\Config::get('site_records_grid', 1) && !$is_sidebar;

        $records = self::getLastCommentsRecordsList($limit);
        if (empty($records)) return;
        
        $data = array(
            'title' => Zira\Locale::t('Last comments'),
            'url' => '',
            'records' => $records,
            'grid' => $is_grid,
            'settings' => array(
                'sidebar' => $is_sidebar
            )
        );

        Zira\View::renderView($data, 'zira/widgets/comments');
    }
}