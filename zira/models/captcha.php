<?php
/**
 * Zira project.
 * captcha.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class Captcha extends Orm {
    public static $table = 'captcha';
    public static $pk = 'id';
    public static $alias = 'cap';

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
        return array();
    }

    public static function register($form_id) {
        $captcha = new self;
        $captcha->form_id = $form_id;
        $captcha->date_created = date('Y-m-d H:i:s');
        $captcha->save();
    }

    public static function isActive($form_id) {
        $total = self::getCollection()
                ->count()
                ->where('form_id','=',$form_id)
                ->and_where('date_created','>=',date('Y-m-d H:i:s',time()-CAPTCHA_SLEEP_TIME))
                ->get('co');

        return ($total >= CAPTCHA_SLEEP_MAX_REQUESTS);
    }

    public static function cleanUp() {
        self::getCollection()
            ->delete()
            ->where('date_created','<',date('Y-m-d',time()-3600))
            ->execute()
            ;
    }
}