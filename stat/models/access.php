<?php
/**
 * Zira project.
 * access.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Models;

use Zira;
use Zira\Orm;

class Access extends Orm {
    public static $table = 'stat_access';
    public static $pk = 'id';
    public static $alias = 'st_a';

    public static function getTable() {
        return self::$table;
    }

    public static function getPk() {
        return self::$pk;
    }

    public static function getAlias() {
        return self::$alias;
    }

    public static function getReferences() {
        return array(
            Zira\Models\Record::getClass() => 'record_id',
            Zira\Models\Category::getClass() => 'category_id'
        );
    }
    
    public static function log() {
        try {
            $record_id = (int)Zira\Page::getRecordId();
            $category_id = Zira\Category::current() ? Zira\Category::current()->id : 0;
            $anonymous_id = Zira\User::getAnonymousUserId();
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $referer = isset($_SERVER['HTTP_REFERER']) ? trim(urldecode($_SERVER['HTTP_REFERER'])) : '';
            
            $is_bot = false;
            if (Zira\Config::get('stat_exclude_bots', 1)) {
                if (stripos($ua, 'bot')!==false || 
                    stripos($ua, 'spider')!==false || 
                    stripos($ua, 'archiver')!==false
                ){
                    $is_bot = true;
                }
                if (!$is_bot && 
                    strlen($referer)>0 && 
                    strpos($referer, 'http://'.$_SERVER['HTTP_HOST'])!==0 && 
                    strpos($referer, 'https://'.$_SERVER['HTTP_HOST'])!==0 && (
                        ($s1=strpos($referer, '/'))===false || 
                        ($s2=strpos($referer, '/', $s1+1))===false || 
                        ($s3=strpos($referer, '/', $s2+1))===false || 
                        strlen($referer)<$s3+2
                    )
                ){ 
                    $is_bot = true;
                }
            }
            
            if ($record_id && !$is_bot) {
                $exists = \Stat\Models\Visitor::getCollection()
                                ->where('record_id','=',$record_id)
                                ->and_where('anonymous_id','=',$anonymous_id)
                                ->limit(1)
                                ->get(0);
                
                if (!$exists) {
                    $visitor_object = new \Stat\Models\Visitor();
                    $visitor_object->record_id = $record_id;
                    $visitor_object->category_id= $category_id;
                    $visitor_object->anonymous_id = $anonymous_id;
                    $visitor_object->access_day = date('Y-m-d');
                    $visitor_object->save();
                    
                    $stat_record = \Stat\Models\Record::getCollection()
                                            ->where('record_id','=',$record_id)
                                            ->get(0, true);

                    $stat_record_object = new \Stat\Models\Record();
                    if (!$stat_record) {
                        $stat_record_object->record_id = $record_id;
                        $stat_record_object->category_id= $category_id;
                        $stat_record_object->views_count = 1;
                    } else {
                        $stat_record_object->loadFromArray($stat_record);
                        $stat_record_object->views_count++;
                    }
                    $stat_record_object->save();
                }
            }

            if (Zira\Config::get('stat_log_ua') && !$is_bot) {
                $ua_exists = \Stat\Models\Agent::getCollection()
                                ->where('ua','=',$ua)
                                ->and_where('anonymous_id','=',$anonymous_id)
                                ->limit(1)
                                ->get(0);
                
                if (!$ua_exists) {
                    $ua_object = new \Stat\Models\Agent();
                    $ua_object->ua = $ua;
                    $ua_object->mobile= Zira\Request::isMobile() ? 1 : 0;
                    $ua_object->anonymous_id = $anonymous_id;
                    $ua_object->access_day = date('Y-m-d');
                    $ua_object->save();
                }
            }

            if (Zira\Config::get('stat_log_access')) {
                $stat = new self;
                $stat->anonymous_id = $anonymous_id;
                $stat->url = trim(urldecode(Zira\Request::uri()));
                $stat->record_id = $record_id;
                $stat->category_id = $category_id;
                $stat->language = Zira\Locale::getLanguage();
                $stat->ip = trim(Zira\Request::ip());
                $stat->ua = $ua;
                $stat->referer = $referer;
                $stat->access_day = date('Y-m-d');
                $stat->access_time = date('Y-m-d H:i:s');
                $stat->save();
            }
        } catch(\Exception $e) {
            // ignore
        }
    }
    
    public static function cleanUp() {
        self::getCollection()
                ->delete()
                ->where('access_day','<',date('Y-m-d', time()-2592000))
                ->execute();
    }
}