<?php
/**
 * Zira project.
 * records.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Records extends Model {
    protected static $_records_publish_callbacks = array();
    protected static $_records_unpublish_callbacks = array();
    protected static $_records_create_callbacks = array();
    protected static $_records_delete_callbacks = array();
    protected static $_record_copy_callbacks = array();
            
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_DELETE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $types = Zira\Request::post('types');

        $category_error = false;
        $category_deleted = 0;
        foreach($data as $i=>$id) {
            if (!array_key_exists($i, $types)) continue;
            if ($types[$i]=='category') {
                $category = new Zira\Models\Category($id);
                if (!$category->loaded()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                };

                $category_empty = true;

                $rows = Zira\Models\Category::getCollection()
                    ->where('name', '=', $category->name)
                    ->union()
                    ->where('name', 'like', $category->name . '/%')
                    ->merge()
                    ->get();

                foreach($rows as $row) {
                    $co=Zira\Models\Record::getCollection()
                        ->count()
                        ->where('category_id','=',$row->id)
                        ->get('co');
                    if ($co>0) {
                        $category_empty = false;
                        break;
                    }
                }

                if ($category_empty) {
                    Zira\Models\Widget::getCollection()
                                ->where('name','=',Zira\Models\Category::WIDGET_CLASS)
                                ->and_where('params','=', $category->id)
                                ->delete()
                                ->execute();

                    Zira\Models\Widget::getCollection()
                                ->where('category_id','=',$category->id)
                                ->delete()
                                ->execute();

                    $subrows = Zira\Models\Category::getCollection()
                                    ->where('name', 'like', $category->name . '/%')
                                    ->get();

                    foreach($subrows as $subcategory) {
                        Zira\Models\Widget::getCollection()
                                    ->where('name','=',Zira\Models\Category::WIDGET_CLASS)
                                    ->and_where('params','=', $subcategory->id)
                                    ->delete()
                                    ->execute();

                        Zira\Models\Widget::getCollection()
                                    ->where('category_id','=',$subcategory->id)
                                    ->delete()
                                    ->execute();
                    }

                    Zira\Models\Category::getCollection()
                        ->delete()
                        ->where('name', 'like', $category->name . '/%')
                        ->execute();

                    $category->delete();
                    
                    $category_deleted++;
                } else {
                    $category_error = true;
                }
            } else if ($types[$i]=='record') {
                $record = new Zira\Models\Record($id);
                if (!$record->loaded()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                };
                $record->delete();

                if ($record->thumb) {
                    $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $record->thumb);
                    if (file_exists($thumb)) @unlink($thumb);
                }
                
                // deleting comments
                Zira\Models\Comment::removeRecordComments($record->id);
                
                // deleting drafts
                Zira\Models\Draft::removeRecordDrafts($record->id);

                // deleting likes
                Zira\Models\Like::removeRecordLikes($record->id);
                
                $gthumbs = array();
                
                // deleting gallery images
                $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$record->id)
                            ->get();

                foreach($images as $image) {
                    if (!$image->thumb) continue;
                    $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image->thumb);
                    if (!file_exists($thumb)) continue;
                    $gthumbs []= $thumb;
                }

                Zira\Models\Image::getCollection()
                            ->delete()
                            ->where('record_id','=',$record->id)
                            ->execute();
                
                // deleting slides
                $slides = Zira\Models\Slide::getCollection()
                            ->where('record_id','=',$record->id)
                            ->get();

                foreach($slides as $slide) {
                    if (!$slide->thumb) continue;
                    $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $slide->thumb);
                    if (!file_exists($thumb)) continue;
                    $gthumbs []= $thumb;
                }

                Zira\Models\Slide::getCollection()
                            ->delete()
                            ->where('record_id','=',$record->id)
                            ->execute();

                // deleting thumbs
                foreach($gthumbs as $thumb) {
                    @unlink($thumb);
                }
                
                // deleting files
                Zira\Models\File::removeRecordFiles($record->id);
                
                // deleting audio
                Zira\Models\Audio::removeRecordAudio($record->id);
                
                // deleting videos
                Zira\Models\Video::removeRecordVideos($record->id);
                
                // deleting search index
                Zira\Models\Search::clearRecordIndex($record);
                
                // running delete hook
                self::runRecordDeleteHook($record);
            }
        }

        Zira\Cache::clear();
        
        if ($category_deleted>0) {
            Zira\Models\Option::raiseVersion();
        }

        if (!$category_error) {
            return array('reload' => $this->getJSClassName());
        } else {
            return array('reload' => $this->getJSClassName(), 'error'=>Zira\Locale::t('Cannot delete category that contains records'));
        }
    }

    public function setCategoryDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category = new Zira\Models\Category($id);
        if (!$category->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$category->description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category->description = Zira\Helper::utf8Clean(strip_tags($description));
        $category->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function setRecordDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$record->description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record->description = Zira\Helper::utf8Clean(strip_tags($description));
        // keep draft
        //$record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function setRecordImage($id, $image) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $image)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$record->image && !$record->thumb && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $record->category_id, $record->id);
        if (!$thumb) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->thumb = $thumb;
        $record->image = str_replace(DIRECTORY_SEPARATOR, '/', $image);
        // keep draft
        //$record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }
    
    public function updateRecordImages($ids) {
        if (empty($ids) || !is_array($ids)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $co = 0;
        foreach($ids as $id) {
            $record = new Zira\Models\Record($id);
            if (!$record->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }

            if (!$record->thumb) continue;
            
            $old_thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $record->thumb);

            if ($record->image) {
                $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $record->image, $record->category_id, $record->id);
            } else if (preg_match('/<img[\x20][^>]*?src[\x20]*[=][\x20]*(?:\'|")([^\'"]+)/',$record->content, $m)) {
                if (strpos($m[1], BASE_URL) === 0) $m[1] = substr($m[1], strlen(BASE_URL));
                $path = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $m[1]);
                $thumb = Zira\Page::createRecordThumb($path, $record->category_id, $record->id);
            } else {
                continue;
            }
            
            if (empty($thumb)) continue;

            if (file_exists($old_thumb)) @unlink($old_thumb);
            
            $record->thumb = $thumb;
            $record->save();
            
            $co++;
        }

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Updated %s thumbs', $co));
    }
    
    public function copyRecords($root, $ids) {
        if (empty($ids) || !is_array($ids)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $result = array();
        foreach($ids as $id) {
            $_result = $this->copyRecord($root, $id);
            if (is_array($_result)) {
                $result = $_result;
                if (array_key_exists('error', $_result)) break;
            }
        }
        return $result;
    }

    public function copyRecord($root, $id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = trim($root,'/');

        if (!empty($root)) {
            $category = Zira\Models\Category::getCollection()
                ->where('name', '=', $root)
                ->get(0);

            if (!$category) {
                return array('error' => Zira\Locale::t('Category not found'));
            }
            $category_id = $category->id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $name = $record->name;
        $co=0;
        do {
            if ($co>0) $_name = $name .'-'.$co;
            else $_name = $name;
            $query = Zira\Models\Record::getCollection();
            $query->count();
            $query->where('category_id', '=', $category_id);
            $query->and_where('language', '=', $record->language);
            $query->and_where('name', '=', $_name);
            $co++;
        } while($query->get('co') > 0);

        $recordArr = $record->toArray();
        unset($recordArr['id']);
        $recordArr['name'] = $_name;
        $recordArr['category_id'] = $category_id;
        $recordArr['thumb'] = null;
        $recordArr['creation_date'] = date('Y-m-d H:i:s');
        $recordArr['modified_date'] = date('Y-m-d H:i:s');
        $new = new Zira\Models\Record();
        $new->loadFromArray($recordArr);
        $new->save();

        if ($new->image) {
            $image = str_replace('/', DIRECTORY_SEPARATOR, $new->image);
            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $new->category_id, $new->id);
            if ($thumb) {
                $new->thumb = $thumb;
                $new->save();
            }
        }

        // copying record slides
        $slides = Zira\Models\Slide::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($slides as $slide) {
            $image = str_replace('/', DIRECTORY_SEPARATOR, $slide->image);
            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $new->category_id, $new->id, false, true);
            if (!$thumb) continue;

            $slideObj = new Zira\Models\Slide();
            $slideObj->record_id = $new->id;
            $slideObj->thumb = $thumb;
            $slideObj->image = $slide->image;
            $slideObj->description = $slide->description;
            $slideObj->save();
        }
        
        // copying record images
        $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($images as $_image) {
            $image = str_replace('/', DIRECTORY_SEPARATOR, $_image->image);
            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $new->category_id, $new->id, true);
            if (!$thumb) continue;

            $imageObj = new Zira\Models\Image();
            $imageObj->record_id = $new->id;
            $imageObj->thumb = $thumb;
            $imageObj->image = $_image->image;
            $imageObj->description = $_image->description;
            $imageObj->save();
        }
        
        // copying record audio
        $files = Zira\Models\Audio::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($files as $file) {
            $fileObj = new Zira\Models\Audio();
            $fileObj->record_id = $new->id;
            $fileObj->path = $file->path;
            $fileObj->url = $file->url;
            $fileObj->embed = $file->embed;
            $fileObj->description = $file->description;
            $fileObj->save();
        }
        
        // copying record videos
        $files = Zira\Models\Video::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($files as $file) {
            $fileObj = new Zira\Models\Video();
            $fileObj->record_id = $new->id;
            $fileObj->path = $file->path;
            $fileObj->url = $file->url;
            $fileObj->embed = $file->embed;
            $fileObj->description = $file->description;
            $fileObj->save();
        }
        
        // copying record files
        $files = Zira\Models\File::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($files as $file) {
            $fileObj = new Zira\Models\File();
            $fileObj->record_id = $new->id;
            $fileObj->path = $file->path;
            $fileObj->url = $file->url;
            $fileObj->download_count = 0;
            $fileObj->description = $file->description;
            $fileObj->save();
        }

        Zira\Models\Search::indexRecord($new);

        // running copy hook
        self::runRecordCopyHook($record, $new);
                
        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }
    
    public function moveRecords($root, $ids) {
        if (empty($ids) || !is_array($ids)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $result = array();
        foreach($ids as $id) {
            $_result = $this->moveRecord($root, $id);
            if (is_array($_result)) {
                $result = $_result;
                if (array_key_exists('error', $_result)) break;
            }
        }
        return $result;
    }

    public function moveRecord($root, $id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = trim($root,'/');

        if (!empty($root)) {
            $category = Zira\Models\Category::getCollection()
                ->where('name', '=', $root)
                ->get(0);

            if (!$category) {
                return array('error' => Zira\Locale::t('Category not found'));
            }
            $category_id = $category->id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        
        $exists = Zira\Models\Record::getCollection()
                        ->count()
                        ->where('category_id','=',$category_id)
                        ->and_where('language','=',$record->language)
                        ->and_where('name','=',$record->name)
                        ->get('co');
        
        if ($exists) {
            return array('error' => Zira\Locale::t('Record with such name already exists'));
        }

        $record->category_id = $category_id;
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function publishRecord($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->published = Zira\Models\Record::STATUS_PUBLISHED;
        $record->save();

        Zira\Models\Search::indexRecord($record);
        
        // running publish hook
        self::runRecordPublishHook($record);

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function publishRecordOnFrontPage($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->front_page = Zira\Models\Record::STATUS_FRONT_PAGE;
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function createCategoryWidget($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category = new Zira\Models\Category(intval($id));
        if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Zira\Models\Category::WIDGET_CLASS;
        $widget->module = 'zira';
        $widget->placeholder = Zira\Models\Category::WIDGET_PLACEHOLDER;
        $widget->params = $category->id;
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', 1));
    }

    public function info($id) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array();
        }

        $info = array();

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) return array();

        /**
        if ($record->front_page) {
            $info[] = '<span class="glyphicon glyphicon-home"></span> ' . Zira\Locale::t('ID: %s', $record->id);
        } else {
            $info[] = '<span class="glyphicon glyphicon-tag"></span> ' . Zira\Locale::t('ID: %s', $record->id);
        }
         **/
        //$info[] = '<span class="glyphicon glyphicon-paperclip"></span> ' . Zira\Helper::html($record->title);
        $info[] = '<span class="glyphicon glyphicon-thumbs-up"></span> ' . Zira\Locale::t('Rating: %s', Zira\Helper::html($record->rating));
        $info[] = '<span class="glyphicon glyphicon-comment"></span> ' . Zira\Locale::t('Comments: %s', Zira\Helper::html($record->comments));
        //$info[] = '<span class="glyphicon glyphicon-time"></span> ' . date(Zira\Config::get('date_format'), strtotime($record->creation_date));

        return array('info'=>$info, 'slides_count'=>$record->slides_count, 'images_count'=>$record->images_count, 'audio_count'=>$record->audio_count, 'video_count'=>$record->video_count, 'files_count'=>$record->files_count);
    }
    
    public static function registerRecordsPublishHook($object, $method) {
        self::$_records_publish_callbacks []= array($object, $method);
    }
    
    public static function registerRecordsUnpublishHook($object, $method) {
        self::$_records_unpublish_callbacks []= array($object, $method);
    }
    
    public static function registerRecordsCreateHook($object, $method) {
        self::$_records_create_callbacks []= array($object, $method);
    }
    
    public static function registerRecordsDeleteHook($object, $method) {
        self::$_records_delete_callbacks []= array($object, $method);
    }
    
    public static function registerRecordCopyHook($object, $method) {
        self::$_record_copy_callbacks []= array($object, $method);
    }
    
    public static function runRecordPublishHook($record) {
        foreach(self::$_records_publish_callbacks as $callback) {
            try {
                call_user_func($callback, $record);
            } catch (Exception $e) {
                // ignore
            }
        }
    }
    
    public static function runRecordUnpublishHook($record) {
        foreach(self::$_records_unpublish_callbacks as $callback) {
            try {
                call_user_func($callback, $record);
            } catch (Exception $e) {
                // ignore
            }
        }
    }
    
    public static function runRecordCreateHook($record) {
        foreach(self::$_records_create_callbacks as $callback) {
            try {
                call_user_func($callback, $record);
            } catch (Exception $e) {
                // ignore
            }
        }
    }
    
    public static function runRecordDeleteHook($record) {
        foreach(self::$_records_delete_callbacks as $callback) {
            try {
                call_user_func($callback, $record);
            } catch (Exception $e) {
                // ignore
            }
        }
    }
    
    public static function runRecordCopyHook($origin, $copy) {
        foreach(self::$_record_copy_callbacks as $callback) {
            try {
                call_user_func($callback, $origin, $copy);
            } catch (Exception $e) {
                // ignore
            }
        }
    }
}