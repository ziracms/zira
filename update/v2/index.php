<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

@ini_set('max_execution_time', 300);

// counting record slides and images
$records_count = \Zira\Models\Record::getCollection()->count()->get('co');
// this should be done separately for larger tables
Zira\Log::write('Going to update records slides and images count. Found '.$records_count.' rows');
if ($records_count <= 100000) {
    $limit = 10;
    $offset = 0;
    while($offset<$records_count) {
        $records = \Zira\Models\Record::getCollection()
                                        ->select('id')
                                        ->order_by('id')
                                        ->limit($limit, $offset)
                                        ->get();

        foreach($records as $record) {
            $slides_co = \Zira\Page::getRecordSlidesCount($record->id);
            $images_co = \Zira\Page::getRecordImagesCount($record->id);
            
            \Zira\Models\Record::getCollection()
                                ->update(array(
                                    'slides_count' => $slides_co,
                                    'images_count' => $images_co
                                ))->where('id', '=', $record->id)
                                ->execute();
        }
        
        unset($records);

        $offset += $limit;
    }
}