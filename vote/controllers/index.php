<?php
/**
 * Zira project.
 * index.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Controllers;

use Zira;
use Vote;

class Index extends Zira\Controller {
    public function index() {
        if (Zira\Request::isPost()) {
            $vote_id = Zira\Request::post('vote_id');
            $options = Zira\Request::post('options');
            $token = Zira\Request::post('token');

            if (empty($vote_id) || empty($options) || !is_array($options) || empty($token)) return;
            if (!Zira\User::checkToken($token)) return;

            $user_id = Zira\User::isAuthorized() ? Zira\User::getCurrent()->id : 0;
            $anonymous_id = Zira\User::getAnonymousUserId();
            if (empty($user_id) && empty($anonymous_id)) return;

            $vote = new Vote\Models\Vote($vote_id);
            if (!$vote->loaded()) return;

            if (!$vote->multiple && count($options)>1) {
                $options = array_slice($options, 0, 1);
            }

            $query = Vote\Models\Voteresult::getCollection()
                            ->count()
                            ->where('vote_id','=',$vote->id);

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
                foreach($options as $option_id) {
//                    $option = new Vote\Models\Voteoption($option_id);
//                    if (!$option->loaded()) continue;
                    $res = new Vote\Models\Voteresult();
                    $res->vote_id = $vote->id;
                    $res->option_id = intval($option_id);
                    $res->user_id = $user_id;
                    $res->anonymous_id = $anonymous_id;
                    $res->creation_date = date('Y-m-d H:i:s');
                    $res->save();
                }
                $vote->votes++;
                $vote->save();
            }

            // results
            $options = \Vote\Models\Voteoption::getCollection()
                                        ->select(array('id','content'))
                                        ->where('vote_id','=',$vote->id)
                                        ->order_by('sort_order', 'asc')
                                        ->get();

            $results = Vote\Models\Voteresult::getCollection()
                            ->select('option_id')
                            ->count()
                            ->where('vote_id','=',$vote->id)
                            ->group_by('option_id')
                            ->get();

            $vals = array();
            foreach($results as $result) {
                $vals[$result->option_id] = $result->co;
            }

            $total = 0;
            for($i=0; $i<count($options); $i++) {
                if (array_key_exists($options[$i]->id, $vals)) {
                    $options[$i]->count = $vals[$options[$i]->id];
                } else {
                    $options[$i]->count = 0;
                }
                $total += $options[$i]->count;
            }

            Zira\View::renderView(array(
                'vote_id' => $vote->id,
                'subject' => $vote->subject,
                'options' => $options,
                'total' => $total,
                'is_ajax' => true
            ), 'vote/results');
        }
    }
}