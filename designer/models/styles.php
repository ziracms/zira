<?php
/**
 * Zira project.
 * styles.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Models;

use Zira;
use Dash;
use Designer;
use Zira\Permission;

class Styles extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Designer\Forms\Style();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $style = new Designer\Models\Style($id);
                if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $style = new Designer\Models\Style();
                $style->theme = Zira\View::getTheme();
                $style->creator_id = Zira\User::getCurrent()->id;
                $style->date_created = date('Y-m-d H:i:s');
            }
            $style->title = $form->getValue('title');
            $language = $form->getValue('language');
            if (empty($language)) $language = null;
            $style->language = $language;
            $category_id = intval($form->getValue('category_id'));
            if ($category_id<0) $category_id = null;
            else if ($category_id==0) $category_id = Zira\Category::ROOT_CATEGORY_ID;
            $style->category_id = $category_id;
            $record_id = intval($form->getValue('record_id'));
            if ($record_id<=0) $record_id = null;
            $style->record_id = $record_id;
            $url = trim($form->getValue('url'));
            if (strlen($url)>0) {
                $url = preg_replace('/(.+?)([?].*)?/','$1',$url);
                if (strpos($url, 'http')===0) {
                    $url = preg_replace('/^http(?:[s])?:\/\/[^\/]+(.*?)/','$1',$url);
                }
                $url = trim($url,'/');
                if (count(Zira\Config::get('languages'))>1) {
                    $url = preg_replace('/^(?:'.implode('|',Zira\Config::get('languages')).')\/(.+?)/','$1',$url);
                }
            }
            if (strlen($url)==0) $url = null;
            $style->url = $url;
            $style->filter = $form->getValue('filter') ? $form->getValue('filter') : null;
            $style->active = (int)$form->getValue('active') ? 1 : 0;

            $style->save();
            
            Zira\Cache::clear();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $style_id) {
            $style = new Designer\Models\Style($style_id);
            if (!$style->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $style->delete();
        }
        
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function copy($id, $title) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $style = new Designer\Models\Style($id);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            
        $values = $style->toArray();
        unset($values['id']);
        $values['title'] = $title;
        $values['active'] = 0;
        $values['date_created'] = date('Y-m-d H:i:s');
        
        $newStyle = new Designer\Models\Style();
        $newStyle->loadFromArray($values);
        $newStyle->save();
        
        return array('reload'=>$this->getJSClassName());
    }
    
    public function activate($id) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $style = new Designer\Models\Style($id);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            
        $style->active = 1;
        $style->save();
        
        return array('reload'=>$this->getJSClassName());
    }
    
    public function autoCompletePage($search) {
        if (empty($search))  return array();
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $records = Zira\Models\Record::getCollection()
                        ->open_query()
                        ->select('id', 'title')
                        ->left_join(Zira\Models\Category::getClass(), array('category_title'=>'title'))
                        ->where('title', 'like', $search.'%')
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->close_query()
                        ->union()
                        ->open_query()
                        ->select('id', 'title')
                        ->left_join(Zira\Models\Category::getClass(), array('category_title'=>'title'))
                        ->where('name', 'like', $search.'%')
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->close_query()
                        ->merge()
                        ->limit(5)
                        ->order_by('id', 'DESC')
                        ->get();
        
        $results = array();
        foreach($records as $record) {
            $title = $record->title;
            if (!empty($record->category_title)) $title = $record->category_title.' / '.$record->title;
            $results []= array(
                'record_id' => $record->id,
                'record_title' => $record->title,
                'title' => $title
            );
        }
        
        return $results;
    }
}