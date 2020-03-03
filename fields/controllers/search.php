<?php
/**
 * Zira project.
 * search.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Controllers;

use Zira;
use Fields;

class Search extends Zira\Controller {
    /**
     * Search action
     */
    public function index() {
        $offset = (int)Zira\Request::get('offset');
        $is_ajax = (int)Zira\Request::get('ajax');
        $limit = Zira\Config::get('records_limit', 10);
        $form = new Fields\Forms\Search();
        $fields = $form->getFieldsArray();
        $form->setFields($fields);
        
        $data = array();
        $found = false;
        $fields_co = 0;
        if (!empty($fields) && $offset>=0 && $form->isValid()) {
            $values = array();
            foreach($fields as $group_id => $fields_group) {
                $values[$group_id] = array();
                foreach($fields_group['fields'] as $field) {
                    $name = $form->getNamePrefix().$field->field_id;
                    $value = $form->getValue($name);
                    if (empty($value)) continue;
                    $values[$group_id][$field->field_id] = array(
                        'value' => $value,
                        'type' => $field->field_type
                    );
                    $fields_co++;
                }
            }
            
            $records = array();
            if ($fields_co>0) {
                $search_type = Zira\Config::get('fields_search_type', Fields\Models\Search::TYPE_SEARCH_OR);
                $records = Fields\Models\Search::searchRecords($values, $limit+1, $offset, $search_type);
            }
            
            if (!empty($records)) {
                $found = true;
                $_data = array(
                        Zira\Page::VIEW_PLACEHOLDER_CLASS => 'search-list'.($is_ajax ? ' xhr-list' : ''),
                        Zira\Page::VIEW_PLACEHOLDER_RECORDS => $records,
                        Zira\Page::VIEW_PLACEHOLDER_SETTINGS => array(
                            'limit' => $limit,
                            //'text' => $form->getValue('text'),
                            'offset' => $offset,
                            'grid' => Zira\Config::get('site_records_grid', 1)
                        )
                );

                if (!$is_ajax) {
                    Zira\Page::setContentView($_data, 'fields/searchresults');
                    $data[Zira\Page::VIEW_PLACEHOLDER_TITLE] = Zira\Locale::tm('Search results', 'fields');
                    $data[Zira\Page::VIEW_PLACEHOLDER_CONTENT] = '';
                } else {
                    Zira\View::renderView($_data, 'fields/searchresults');
                }
            } else if ($fields_co>0) {
                $form->setError(Zira\Locale::tm('Your search did not match any documents', 'fields'));
            }
        }

        if (!$is_ajax) {
            Zira\Page::addTitle(Zira\Locale::t('Search'));
            Zira\Page::addBreadcrumb('fields/search', Zira\Locale::t('Search'));

            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT_TOP, array('error' => $form->getError()), 'fields/searchpage');
            Zira\Page::render($data);
        }
    }
}