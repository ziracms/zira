<?php
/**
 * Zira project.
 * votes.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Models;

use Zira;
use Dash;
use Vote;
use Zira\Permission;

class Votes extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Vote\Forms\Vote();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $vote = new Vote\Models\Vote($id);
                if (!$vote->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $vote = new Vote\Models\Vote();
                $vote->placeholder = $form->getValue('placeholder');
                $vote->creator_id = Zira\User::getCurrent()->id;
                $vote->date_created = date('Y-m-d H:i:s');
                $vote->votes = 0;
            }
            $vote->subject = $form->getValue('subject');
            $vote->multiple = (int)$form->getValue('multiple');

            $vote->save();

            if (empty($id)) {
                $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

                $widget = new Zira\Models\Widget();
                $widget->name = Vote\Models\Vote::WIDGET_CLASS;
                $widget->module = 'vote';
                $widget->placeholder = $form->getValue('placeholder');
                $widget->params = $vote->id;
                $widget->category_id = null;
                $widget->sort_order = ++$max_order;
                $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
                $widget->save();
            }

            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function install($votes) {
        if (empty($votes) || !is_array($votes)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $widgets = array();
        $rows = Zira\Models\Widget::getCollection()
                                ->where('name','=',Vote\Models\Vote::WIDGET_CLASS)
                                ->get()
                                ;
        foreach($rows as $row) {
            if (!is_numeric($row->params)) continue;
            $widgets[] = $row->params;
        }

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $co=0;
        foreach($votes as $vote_id) {
            $vote = new Vote\Models\Vote(intval($vote_id));
            if (!$vote->loaded()) continue;
            if (in_array($vote->id,$widgets)) continue;
            $widget = new Zira\Models\Widget();
            $widget->name = Vote\Models\Vote::WIDGET_CLASS;
            $widget->module = 'vote';
            $widget->placeholder = $vote->placeholder;
            $widget->params = $vote->id;
            $widget->category_id = null;
            $widget->sort_order = ++$max_order;
            $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
            $widget->save();

            $co++;
        }

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', $co), 'reload'=>$this->getJSClassName());
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $vote_id) {
            $vote = new Vote\Models\Vote($vote_id);
            if (!$vote->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $vote->delete();

            Zira\Models\Widget::getCollection()
                                ->where('name','=',Vote\Models\Vote::WIDGET_CLASS)
                                ->and_where('params','=',$vote_id)
                                ->delete()
                                ->execute();

            Vote\Models\Voteoption::getCollection()
                                ->where('vote_id','=',$vote_id)
                                ->delete()
                                ->execute();

            Vote\Models\Voteresult::getCollection()
                                ->where('vote_id','=',$vote_id)
                                ->delete()
                                ->execute();
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function option($vote_id, $content, $option_id = null) {
        if (empty($vote_id) || empty($content)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $vote = new Vote\Models\Vote(intval($vote_id));
        if (!$vote->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        if ($option_id === null) {
            $max_order = Vote\Models\Voteoption::getCollection()->max('sort_order')->get('mx');

            $option = new Vote\Models\Voteoption();
            $option->vote_id = $vote->id;
            $option->sort_order = ++$max_order;
        } else {
            $option = new Vote\Models\Voteoption($option_id);
            if (!$option->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        }

        $option->content = strip_tags($content);
        $option->save();

        return array('reload' => Dash\Dash::getInstance()->getWindowJSName(Vote\Windows\Options::getClass()));
    }

    public function drag($vote_id, $options, $orders) {
        if (empty($vote_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (empty($options) || !is_array($options) || count($options)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($options)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_options = array();
        $_orders = array();
        foreach($options as $id) {
            $_option = new Vote\Models\Voteoption($id);
            if (!$_option->loaded() || $_option->vote_id != $vote_id) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_options []= $_option;
            $_orders []= $_option->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_options as $index=>$option) {
            $option->sort_order = intval($orders[$index]);
            $option->save();
        }

        return array('reload'=>Dash\Dash::getInstance()->getWindowJSName(Vote\Windows\Options::getClass()));
    }

    public function deleteOptions($vote_id, $options) {
        if (empty($vote_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (empty($options) || !is_array($options)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($options as $option_id) {
            $option = new Vote\Models\Voteoption($option_id);
            if (!$option->loaded() || $option->vote_id != $vote_id) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $option->delete();
        }

        return array('reload' => Dash\Dash::getInstance()->getWindowJSName(Vote\Windows\Options::getClass()));
    }

    public function info($id) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array();
        }

        $info = array();

        $vote = new Vote\Models\Vote($id);
        if (!$vote->loaded()) return array();

        $info[] = '<span class="glyphicon glyphicon-tag"></span> ' . Zira\Helper::html($vote->subject);
        $info[] = '<span class="glyphicon glyphicon-thumbs-up"></span> ' . Zira\Locale::tm('Votes: %s', 'vote', Zira\Helper::html($vote->votes));
        $info[] = '<span class="glyphicon glyphicon-time"></span> ' . date(Zira\Config::get('date_format'), strtotime($vote->date_created));

        return $info;
    }

    public function results($vote_id) {
        $options = \Vote\Models\Voteoption::getCollection()
                                    ->select(array('id','content'))
                                    ->where('vote_id','=',$vote_id)
                                    ->order_by('sort_order', 'asc')
                                    ->get();

        $results = Vote\Models\Voteresult::getCollection()
                        ->select('option_id')
                        ->count()
                        ->where('vote_id','=',$vote_id)
                        ->group_by('option_id')
                        ->get();

        $vals = array();
        foreach($results as $result) {
            $vals[$result->option_id] = $result->co;
        }

        $return = array();
        foreach($options as $option) {
            if (array_key_exists($option->id, $vals)) {
                $count = $vals[$option->id];
            } else {
                $count = 0;
            }
            $return []= '&bull;&nbsp;'.$option->content.' &mdash; '.$count;
        }

        return $return;
    }
}