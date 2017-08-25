<?php
/**
 * Zira project.
 * permission.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class Permission {
    const TO_ACCESS_DASHBOARD = 'Access system dashboard';
    const TO_EXECUTE_TASKS = 'Execute system tasks';
    const TO_CHANGE_OPTIONS = 'Change system options';
    const TO_CHANGE_LAYOUT = 'Change theme layout';
    const TO_CREATE_USERS = 'Create users';
    const TO_EDIT_USERS = 'Edit users';
    const TO_DELETE_USERS = 'Delete users';
    const TO_UPLOAD_FILES = 'Upload files';
    const TO_DELETE_FILES = 'Delete files';
    const TO_VIEW_FILES = 'View files list';
    const TO_UPLOAD_IMAGES = 'Upload images';
    const TO_DELETE_IMAGES = 'Delete images';
    const TO_VIEW_IMAGES = 'View images list';
    const TO_CREATE_RECORDS = 'Create records';
    const TO_EDIT_RECORDS = 'Edit records';
    const TO_DELETE_RECORDS = 'Delete records';
    const TO_VIEW_RECORDS = 'View records list';
    const TO_VIEW_RECORD = 'View record';
    const TO_MODERATE_COMMENTS = 'Moderate comments';
    const TO_DOWNLOAD_FILES = 'Download files';
    const TO_VIEW_GALLERY = 'View gallery';
    const TO_LISTEN_AUDIO = 'Listen to audio';
    const TO_VIEW_VIDEO = 'View video';

    protected static $_loaded = array();

    public static function getPermissionsArray() {
        return array(
            self::TO_ACCESS_DASHBOARD,
            self::TO_EXECUTE_TASKS,
            self::TO_CHANGE_OPTIONS,
            self::TO_CHANGE_LAYOUT,
            self::TO_CREATE_USERS,
            self::TO_EDIT_USERS,
            self::TO_DELETE_USERS,
            self::TO_UPLOAD_FILES,
            self::TO_DELETE_FILES,
            self::TO_VIEW_FILES,
            self::TO_UPLOAD_IMAGES,
            self::TO_DELETE_IMAGES,
            self::TO_VIEW_IMAGES,
            self::TO_CREATE_RECORDS,
            self::TO_EDIT_RECORDS,
            self::TO_DELETE_RECORDS,
            self::TO_MODERATE_COMMENTS,
            self::TO_VIEW_RECORDS,
            self::TO_VIEW_RECORD,
            self::TO_DOWNLOAD_FILES,
            self::TO_VIEW_GALLERY,
            self::TO_LISTEN_AUDIO,
            self::TO_VIEW_VIDEO
        );
    }

    public static function loadOnceGroupPermissions($group_id) {
        if (isset(self::$_loaded[$group_id])) return;
        $rows = Models\Permission::getGroupPermissions($group_id);
        $permissions = array();
        foreach($rows as $row) {
            $permissions[$row->name] = $row->allow;
        }
        self::$_loaded[$group_id] = $permissions;
    }

    public static function check($permission, $user = null) {
        if ($user===null && User::isAuthorized()) $user = User::getCurrent();
        if (!$user) return false;
        self::loadOnceGroupPermissions($user->group_id);
        if (!isset(self::$_loaded[$user->group_id])) return false;
        if (!isset(self::$_loaded[$user->group_id][$permission])) return false;
        return !empty(self::$_loaded[$user->group_id][$permission]);
    }
}