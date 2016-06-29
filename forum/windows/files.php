<?php
/**
 * Zira project.
 * files.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Files extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-folder-open';
    protected static $_title = 'Attached files';

    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->setData(array(
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
        ));
    }

    public static function extractFiles($row) {
        $files = array();
        for ($i=1; $i<=\Forum\Models\File::MAX_FILES_COUNT; $i++) {
            $field = 'path'.$i;
            if ($row->{$field}) {
                $files[$i] = $row->{$field};
            }
        }
        return $files;
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) && !Permission::check(\Forum\Forum::PERMISSION_MODERATE)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $this->total = \Forum\Models\File::getCollection()
                                    ->count()
                                    ->get('co');

        $this->pages = ceil($this->total / $this->limit);
        if ($this->page > $this->pages) $this->page = $this->pages;
        if ($this->page < 1) $this->page = 1;

        $files = \Forum\Models\File::getCollection()
                                    ->select(\Forum\Models\File::getFields())
                                    ->left_join(Zira\Models\User::getClass(), array('user_login'=>'username'))
                                    ->order_by('id', $this->order)
                                    ->limit($this->limit, ($this->page - 1) * $this->limit)
                                    ->get();

        $items = array();
        foreach($files as $file) {
            $_files = self::extractFiles($file);
            foreach($_files as $field_num=>$path) {
                $p = strrpos($path, DIRECTORY_SEPARATOR);
                if ($p!==false) $filename = substr($path, $p+1);
                else $filename = $path;
                $content = Zira\Helper::html($filename);
                $username = $file->user_login ? $file->user_login : Zira\Locale::tm('User deleted', 'forum');
                $items[] = $this->createBodyFileItem($content, $username, $file->id.'_'.$field_num, 'desk_call(dash_forum_file_show, this);', false, array('type' => 'txt', 'path'=>Zira\Helper::baseUrl(UPLOADS_DIR.'/'.str_replace(DIRECTORY_SEPARATOR, '/', $path))));
            }
        }
        $this->setBodyItems($items);

        $this->setData(array(
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order
        ));
    }
}