<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Widgets;

use Zira;

class Vote extends Zira\Widget {
    protected $_title = 'Vote';
    protected static $_titles;

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT_BOTTOM);
    }

    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = \Vote\Models\Vote::getCollection()->get();
            foreach($rows as $row) {
                self::$_titles[$row->id] = $row->subject;
            }
        }
        return self::$_titles;
    }

    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            $titles = $this->getTitles();
            if (empty($titles) || !array_key_exists($this->getData(), $titles)) return parent::getTitle();
            return Zira\Locale::t('Vote') . ' - ' . $titles[$id];
        } else {
            return parent::getTitle();
        }
    }

    protected function _render() {
        $id = $this->getData();
        $user_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
        $anonymous_id = Zira\User::getAnonymousUserId();
        if (empty($user_id) && empty($anonymous_id)) return;

        $options = \Vote\Models\Voteoption::getCollection()
                                        ->select(array('id'=>'id','content'=>'content'))
                                        ->join(\Vote\Models\Vote::getClass(), array('vote_id'=>'id','subject'=>'subject', 'multiple'=>'multiple'))
                                        ->where('vote_id','=',$id)
                                        ->order_by('sort_order', 'asc')
                                        ->get();

        if (!$options || count($options)==0) return;

        $vote_id = $options[0]->vote_id;
        $subject = $options[0]->subject;
        $multiple = $options[0]->multiple;

        $is_sidebar = $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_LEFT || $this->getPlaceholder() == Zira\View::VAR_SIDEBAR_RIGHT;



        $query = \Vote\Models\Voteresult::getCollection()
                        ->count()
                        ->where('vote_id','=',$id);

        if (!empty($user_id)) {
            $query->and_where();
            $query->open_where();
            $query->where('user_id','=',$user_id);
            $query->or_where('anonymous_id','=',$anonymous_id);
            $query->close_where();
        } else if (!empty($anonymous_id)) {
            $query->and_where('anonymous_id','=',$anonymous_id);
        }

        $co = $query->get('co');

        if ($co==0) {
            Zira\View::renderView(array(
                'token' => Zira\User::getToken(),
                'vote_id' => $vote_id,
                'subject' => $subject,
                'multiple' => $multiple,
                'options' => $options,
                'is_sidebar' => $is_sidebar
            ), 'vote/options');
        } else {
            // results
            $results = \Vote\Models\Voteresult::getCollection()
                ->select('option_id')
                ->count()
                ->where('vote_id', '=', $id)
                ->group_by('option_id')
                ->get();

            $vals = array();
            foreach ($results as $result) {
                $vals[$result->option_id] = $result->co;
            }

            $total = 0;
            for ($i = 0; $i < count($options); $i++) {
                if (array_key_exists($options[$i]->id, $vals)) {
                    $options[$i]->count = $vals[$options[$i]->id];
                } else {
                    $options[$i]->count = 0;
                }
                $total += $options[$i]->count;
            }

            Zira\View::renderView(array(
                'vote_id' => $vote_id,
                'subject' => $subject,
                'options' => $options,
                'total' => $total,
                'is_ajax' => false,
                'is_sidebar' => $is_sidebar
            ), 'vote/results');
        }
    }
}