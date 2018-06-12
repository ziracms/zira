<?php
/**
 * Zira project.
 * record.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Widgets;

use Zira;

class Record extends Zira\Widget {
    protected $_title = 'Record views count';

    protected function _init() {
        $this->setDynamic(false);
        $this->setCaching(false);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_CONTENT);
    }

    protected function _render() {
        if (Zira\Router::getModule()==DEFAULT_MODULE && Zira\Router::getController()==DEFAULT_CONTROLLER && Zira\Router::getAction()==DEFAULT_ACTION) return;
        $record_id = (int)Zira\Page::getRecordId();
        if (!$record_id) return;
        
        $views = 0;
        
        $row = \Stat\Models\Record::getCollection()
                        ->where('record_id','=',$record_id)
                        ->get(0);
        
        if ($row) {
            $views = $row->views_count;
        }
        
        Zira\View::renderView(array(
            'views' => $views,
        ), 'stat/record');
    }
}