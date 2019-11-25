<?php
/**
* Zira project.
* tagscloud.php
* (c)2019 https://github.com/ziracms/zira
*/

namespace Zira\Widgets;

use Zira;

class Tagscloud extends Zira\Widget {
    protected $_title = 'Tags cloud';
    
    protected function _init() {
        $this->setCaching(true);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_SIDEBAR_RIGHT);
    }
    
    protected function _render() {
        $limit = 20;
        $query = Zira\Models\Tag::getCollection()
                            ->select('tag')
                            ->count()
                            ->where('language', '=', Zira\Locale::getLanguage())
                            ->group_by('tag')
                            ->random()
                            ->limit($limit);

        $rows = $query->get();
        if (empty($rows)) return;

        $tags = array();
        $max = 0;
        foreach($rows as $row) {
            $tags[$row->tag] = $row->co;
            if ($row->co > $max) $max = $row->co;
        }
        
        Zira\View::renderView(array(
            'tags' => $tags,
            'max' => $max
        ), 'zira/widgets/tagscloud');
    }
}