<?php
/**
 * Zira project.
 * user.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Models;

use Zira\Orm;

class User extends Orm {
    const STATUS_VERIFIED = 1;
    const STATUS_NOT_VERIFIED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_SUBSCRIBED = 0;

    public static $table = 'users';
    public static $pk = 'id';
    public static $alias = 'usr';

    public static function getFields() {
        return array(
            'id',
            'email',
            'username',
            'password',
            'group_id',
            'image',
            'firstname',
            'secondname',
            'dob',
            'phone',
            'country',
            'city',
            'address',
            'date_created',
            'date_logged',
            'messages',
            'comments',
            'posts',
            'subscribed',
            'verified',
            'active',
            'vcode',
            'code',
            'token'
        );
    }

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
            Group::getClass() => 'group_id'
        );
    }

    protected static function getJoinedCollection() {
        return self::getCollection()
            ->join(Group::getClass(), array('group_name'=>'name'))
            ;
    }

    protected static function getActiveCollection()
    {
        $collection = self::getJoinedCollection();
        return $collection
            ->where('verified', '=', self::STATUS_VERIFIED)
            ->and_where('active', '=', self::STATUS_ACTIVE)
            ->and_where('active', '=', Group::STATUS_ACTIVE, Group::getAlias());
    }

    public static function findUser($username_or_email_or_id) {
        $collection = self::getJoinedCollection();
        $collection->select(self::getFields());

        if (is_int($username_or_email_or_id)) {
            $collection->where(self::getPk(),'=',$username_or_email_or_id);
        } else if (strpos($username_or_email_or_id,'@')!==false) {
            $collection->where('email','=',$username_or_email_or_id);
        } else {
            $collection->where('username','=',$username_or_email_or_id);
        }

        return $collection->get(0);
    }

    public static function findActiveUser($username_or_email_or_id) {
        $collection = self::getActiveCollection();
        $collection->select(self::getFields());

        if (is_int($username_or_email_or_id)) {
            $collection->and_where(self::getPk(),'=',$username_or_email_or_id);
        } else if (strpos($username_or_email_or_id,'@')!==false) {
            $collection->and_where('email','=',$username_or_email_or_id);
        } else {
            $collection->and_where('username','=',$username_or_email_or_id);
        }

        return $collection->get(0);
    }

    public static function findAuthUser($username_or_email) {
        $collection = self::getJoinedCollection();
        $collection->select(self::getFields())
            ->where('active','=',self::STATUS_ACTIVE)
            ->and_where('active','=',Group::STATUS_ACTIVE, Group::getAlias())
            ;

        if (strpos($username_or_email,'@')!==false) {
            $collection->and_where('email','=',$username_or_email);
        } else {
            $collection->and_where('username','=',$username_or_email);
        }

        return $collection->get(0);
    }

    public static function getActiveUsersCount() {
        $collection = self::getActiveCollection();
        return $collection->count()->get('co');
    }

    public static function getActiveUsers($limit=10, $offset=0, $order='DESC') {
        $collection = self::getActiveCollection();
        return $collection->select(self::getFields())
            ->order_by(self::getPk(), $order)
            ->limit($limit,$offset)
            ->get()
            ;
    }

    public static function getAllUsersCount() {
        $collection = self::getJoinedCollection();
        return $collection->count()->get('co');
    }

    public static function getAllUsers($limit=10, $offset=0, $order='DESC') {
        $collection = self::getJoinedCollection();
        return $collection->select(self::getFields())
            ->order_by(self::getPk(), $order)
            ->limit($limit,$offset)
            ->get()
            ;
    }

    public static function getGroupAllUsersCount($group_id) {
        $collection = self::getJoinedCollection();
        return $collection->count()
                    ->where('group_id','=',$group_id)
                    ->get('co');
    }

    public static function getGroupAllUsers($group_id, $limit=10, $offset=0, $order='DESC') {
        $collection = self::getJoinedCollection();
        return $collection->select(self::getFields())
            ->where('group_id','=',$group_id)
            ->order_by(self::getPk(), $order)
            ->limit($limit,$offset)
            ->get()
            ;
    }

    public static function getSearchUsersCount($username_or_email_or_id, $group_id=null) {
        $collection = self::getJoinedCollection();
        $collection->count();

        $collection->open_where();
        if (is_numeric($username_or_email_or_id)) {
            $collection->where(self::getPk(),'=',intval($username_or_email_or_id));
        } else if (strpos($username_or_email_or_id,'@')!==false) {
            $collection->where('email','=',$username_or_email_or_id);
        } else {
            $collection->where('username','like','%'.$username_or_email_or_id.'%');
            $collection->or_where('firstname','like','%'.$username_or_email_or_id.'%');
            $collection->or_where('secondname','like','%'.$username_or_email_or_id.'%');
        }
        $collection->close_where();

        if (!empty($group_id)) {
            $collection->and_where('group_id','=',$group_id);
        }

        return $collection->get('co');
    }

    public static function searchUsers($username_or_email_or_id, $limit=10, $offset=0, $order='DESC', $group_id=null) {
        $collection = self::getJoinedCollection();
        $collection->select(self::getFields());

        $collection->open_where();
        if (is_numeric($username_or_email_or_id)) {
            $collection->where(self::getPk(),'=',intval($username_or_email_or_id));
        } else if (strpos($username_or_email_or_id,'@')!==false) {
            $collection->where('email','=',$username_or_email_or_id);
        } else {
            $collection->where('username','like','%'.$username_or_email_or_id.'%');
            $collection->or_where('firstname','like','%'.$username_or_email_or_id.'%');
            $collection->or_where('secondname','like','%'.$username_or_email_or_id.'%');
        }
        $collection->close_where();

        if (!empty($group_id)) {
            $collection->and_where('group_id','=',$group_id);
        }

        return $collection->order_by(self::getPk(), $order)
                            ->limit($limit,$offset)
                            ->get();
    }
}