<?php
/**
 * Zira project.
 * search.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Controllers;

use Zira;

class Search extends Zira\Controller {
    /**
     * Search action
     */
    public function index() {
        $offset = (int)Zira\Request::get('offset');
        $is_ajax = (int)Zira\Request::get('ajax');
        $is_simple = (int)Zira\Request::get('simple');
        $limit = $is_simple ? 3 : Zira\Config::get('records_limit', 10);
        $form = new Zira\Forms\Search('search-form-extended', true);
        $form->setExtended(true);
        $data = array();
        $found = false;
        if ($form->getValue('text') && $offset>=0 && $form->isValid()) {
            if (!Zira\Config::get('search_fulltext', 0)) {
                if (!$is_simple) {
                    $records = Zira\Models\Search::getRecords($form->getValue('text'), $limit + 1, $offset);
                } else {
                    $records = Zira\Models\Search::getRecordsSorted($form->getValue('text'), $limit);
                }
            } else {
                $records = Zira\Models\Search::getRecordsFullText($form->getValue('text'), !$is_simple ? $limit + 1 : $limit, !$is_simple ? $offset : 0);
            }
            if (!empty($records)) {
                $found = true;
                $_data = array(
                        Zira\Page::VIEW_PLACEHOLDER_CLASS => 'search-list'.($is_ajax ? ' xhr-list' : ''),
                        Zira\Page::VIEW_PLACEHOLDER_RECORDS => $records,
                        Zira\Page::VIEW_PLACEHOLDER_SETTINGS => array(
                            'limit' => $limit,
                            'text' => $form->getValue('text'),
                            'offset' => $offset,
                            'simple' => $is_simple,
                            'grid' => Zira\Config::get('site_records_grid', 1)
                        )
                );

                if (!$is_ajax) {
                    Zira\Page::setContentView($_data, 'zira/search-results');
                    $data[Zira\Page::VIEW_PLACEHOLDER_TITLE] = Zira\Locale::t('Search results');
                    $data[Zira\Page::VIEW_PLACEHOLDER_CONTENT] = '';
                } else {
                    Zira\View::renderView($_data, 'zira/search-results');
                }
            } else {
                $form->setValue('text','');
                $form->setError(Zira\Locale::t('Your search did not match any documents'));
            }
        } else {
            $form->setValue('text','');
        }

        if (!$is_ajax) {
            Zira\Page::addTitle(Zira\Locale::t('Search'));
            Zira\Page::addBreadcrumb('search', Zira\Locale::t('Search'));

//            Zira\Page::setLayout(Zira\View::LAYOUT_ALL_SIDEBARS);
//            Zira\View::setRenderDbWidgets(false);
            
            Zira\View::addPlaceholderView(Zira\View::VAR_CONTENT_TOP, array('form' => $form, 'found' => $found), 'zira/search');
            Zira\Page::render($data);
        }
    }

    public function tags() {
        $offset = (int)Zira\Request::get('offset');
        $is_ajax = (int)Zira\Request::get('ajax');
        $text = trim(Zira\Request::get('text'));
        $limit = Zira\Config::get('records_limit', 10);
        $data = array();
        $found = false;
        if (!empty($text) && mb_strlen($text, CHARSET)<=255 && $offset>=0) {
            $records = Zira\Models\Tag::getRecords($text, $limit + 1, $offset);
            if (!empty($records)) {
                $found = true;
                $_data = array(
                    Zira\Page::VIEW_PLACEHOLDER_CLASS => 'search-list'.($is_ajax ? ' xhr-list' : ''),
                    Zira\Page::VIEW_PLACEHOLDER_RECORDS => $records,
                    Zira\Page::VIEW_PLACEHOLDER_SETTINGS => array(
                        'limit' => $limit,
                        'text' => $text,
                        'offset' => $offset,
                        'simple' => false,
                        'grid' => Zira\Config::get('site_records_grid', 1)
                    )
                );
                
                if (!$is_ajax) {
                    Zira\Page::setContentView($_data, 'zira/tags-results');
                    $data[Zira\Page::VIEW_PLACEHOLDER_TITLE] = Zira\Locale::t('Tag').': '.$text;
                    $data[Zira\Page::VIEW_PLACEHOLDER_CONTENT] = '';
                } else {
                    Zira\View::renderView($_data, 'zira/tags-results');
                }
            } else {
                Zira\Response::notFound();
            }
        } else {
            Zira\Response::notFound();
        }
        
        if (!$is_ajax) {
            Zira\Page::addTitle(Zira\Locale::t('Tag').': '.$text);
            Zira\Page::addBreadcrumb('tags', Zira\Locale::t('Tags'));
            
//            Zira\Page::setLayout(Zira\View::LAYOUT_ALL_SIDEBARS);
//            Zira\View::setRenderDbWidgets(false);
            
            Zira\Page::render($data);
        }
    }
}