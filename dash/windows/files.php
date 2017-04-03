<?php
/**
 * Zira project.
 * files.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Files extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-hdd';
    protected static $_title = 'File Manager';

    protected $_help_url = 'zira/help/file-manager';

    public $page = 0;
    public $pages = 0;
    public $order = 'asc';
    protected  $limit = 50;
    protected $total = 0;

    const THUMBS_FOLDER = 'filemanager';
    protected $_hidden_folders = array('users', 'thumbs');

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setNoCache(true);

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New folder'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_files_mkdir, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Upload from URL'), 'glyphicon glyphicon-link', 'desk_call(dash_files_upload_url, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Download'), 'glyphicon glyphicon-circle-arrow-down', 'desk_call(dash_files_download, this);', 'call', false, array('typo'=>'download'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_files_copy, this);', 'call')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Move'), 'glyphicon glyphicon-scissors', 'desk_call(dash_files_move, this);', 'call')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Rename'), 'glyphicon glyphicon-tag', 'desk_call(dash_files_rename, this);', 'call')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New folder'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_files_mkdir, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Upload from URL'), 'glyphicon glyphicon-link', 'desk_call(dash_files_upload_url, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Download'), 'glyphicon glyphicon-circle-arrow-down', 'desk_call(dash_files_download, this);', 'call', false, array('typo'=>'download'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_files_copy, this);', 'call')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Rename'), 'glyphicon glyphicon-tag', 'desk_call(dash_files_rename, this);', 'call')
        );

        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(dash_files_open, this);'
            )
        );
        $this->addDefaultOnLoadScript(
            'desk_call(dash_files_load, this);'
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_files_edit, this);'
            )
        );
        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_files_drop, this, element);'
            )
        );
        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                $this->get_on_select_js()
            )
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Up'), 'glyphicon glyphicon-level-up', 'desk_call(dash_files_up, this);', 'level', true)
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('New folder'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_files_mkdir, this);', 'create')
        );
        $this->setSidebarContent('<div class="filemanager-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');
        $uploadForm = Zira\Helper::tag_open('span',array('style'=>'display:block;position:relative;height:100%;overflow:hidden;padding:0px;font-weight:normal')).
                    Zira\Helper::tag_open('span',array('style'=>'display:block;position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;filter:alpha(opacity=0);z-index:1;padding:0px;')).
                    new Dash\Forms\Upload().
                    Zira\Helper::tag_close('span').
                    Zira\Helper::tag('span',null,array('class'=>'glyphicon glyphicon-open-file')).' '.
                    Zira\Locale::t('Upload').
                    Zira\Helper::tag_close('span');

        $this->addDefaultSidebarItem(
            $this->createSidebarItem($uploadForm, null, '', 'create')
        );

        if ($this->is_archive_supported()) {
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownSeparator()
            );
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem(Zira\Locale::t('Create archive'), 'glyphicon glyphicon-briefcase', 'desk_call(dash_files_pack, this);', 'delete')
            );
            $this->addDefaultMenuDropdownItem(
                $this->createMenuDropdownItem(Zira\Locale::t('Extract'), 'glyphicon glyphicon-inbox', 'desk_call(dash_files_unpack, this);', 'call', false, array('typo'=>'archive'))
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuSeparator()
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem(Zira\Locale::t('Create archive'), 'glyphicon glyphicon-briefcase', 'desk_call(dash_files_pack, this);', 'delete')
            );
            $this->addDefaultContextMenuItem(
                $this->createContextMenuItem(Zira\Locale::t('Extract'), 'glyphicon glyphicon-inbox', 'desk_call(dash_files_unpack, this);', 'call', false, array('typo'=>'archive'))
            );
        }

        $this->setMenuItems(array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('File'), array(
                $this->createMenuDropdownItem(Zira\Locale::t('Create text file'), 'glyphicon glyphicon-file', 'desk_call(dash_files_new_text_file, this);', 'create'),
                $this->createMenuDropdownItem(Zira\Locale::t('Create HTML file'), 'glyphicon glyphicon-file', 'desk_call(dash_files_new_html_file, this);', 'create'),
                $this->createMenuDropdownSeparator(),
                $this->createMenuDropdownItem(Zira\Locale::t('Open as text'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_files_notepad, this);', 'call', false, array('typo'=>'notepad')),
                $this->createMenuDropdownItem(Zira\Locale::t('Show image'), 'glyphicon glyphicon-picture', 'desk_call(dash_files_show_image, this);', 'edit', true, array('typo'=>'show_image'))
            ))
        ));

        $this->setData(array(
            'max_upload_size' => $this->get_max_upload_size(),
            'max_upload_files' => $this->get_max_upload_files()
        ));

        $this->addStrings(array(
            'Information',
            'Enter name',
            'Enter URL address',
            'Enter folder path',
            'Enter archive name'
        ));

        $this->includeJS('dash/files');
    }

    protected function get_body_item_callback_js() {
        return 'desk_window_edit_item(this);';
    }

    protected function get_on_select_js() {
        return 'desk_call(dash_files_select, this);';
    }

    public function get_max_upload_size() {
        $psize = trim((string)@ini_get('post_max_size'));
        $usize = trim((string)@ini_get('upload_max_filesize'));

        $m = strtolower(substr($psize, -1));
        if ($m=='g') $psize *= 1073741824;
        else if ($m=='m') $psize *= 1048576;
        else if ($m=='k') $psize *= 1024;

        $m = strtolower(substr($usize, -1));
        if ($m=='g') $usize *= 1073741824;
        else if ($m=='m') $usize *= 1048576;
        else if ($m=='k') $usize *= 1024;

        return min($psize, $usize);
    }

    public function get_max_upload_files() {
        return (int)@ini_get('max_file_uploads');
    }

    public function get_image_size($abs_path) {
        $p = strrpos($abs_path,'.');
        if ($p===false) return false;
        $ext = substr($abs_path, $p+1);
        if (!in_array($ext, array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF'))) return false;
        $size=@getimagesize($abs_path);
        if (!$size) return false;
        if ($size[2]!=IMAGETYPE_JPEG && $size[2]!=IMAGETYPE_PNG && $size[2]!=IMAGETYPE_GIF) return false;
        return $size;
    }

    protected function get_image_thumb($rel_path) {
        $rel_path = trim($rel_path, DIRECTORY_SEPARATOR);
        if (strpos($rel_path, UPLOADS_DIR . DIRECTORY_SEPARATOR)!==0) return $rel_path;
        $p = strpos($rel_path, DIRECTORY_SEPARATOR);
        if ($p===false) return $rel_path;
        $_rel_path = substr($rel_path, $p+1);
        if (empty($_rel_path)) return $rel_path;
        if (strpos($_rel_path, Dash\Windows\Files::THUMBS_FOLDER)===0) return $rel_path;
        $src_path = ROOT_DIR . DIRECTORY_SEPARATOR . $rel_path;
        $save_path = Zira\File::getAbsolutePath(Dash\Windows\Files::THUMBS_FOLDER);
        if (file_exists($save_path . DIRECTORY_SEPARATOR . $_rel_path)) return UPLOADS_DIR . '/' .Dash\Windows\Files::THUMBS_FOLDER . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $_rel_path);
        $p = strrpos($_rel_path,DIRECTORY_SEPARATOR);
        if ($p!==false) {
            $dir = substr($_rel_path, 0, $p);
            $save_path = Zira\File::getAbsolutePath(Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $dir);
        }
        $dst_path = $save_path . DIRECTORY_SEPARATOR . basename($_rel_path);
        if (!Zira\Image::createThumb($src_path, $dst_path, 60, 60)) return $rel_path;
        return UPLOADS_DIR . '/' .Dash\Windows\Files::THUMBS_FOLDER . '/' . str_replace(DIRECTORY_SEPARATOR, '/',$_rel_path);
    }

    protected function is_archive_supported() {
        return class_exists('ZipArchive');
    }

    protected function is_archive($file) {
        return substr($file,-4)=='.zip';
    }

    protected function is_txt($file) {
        return substr($file,-4)=='.txt';
    }

    protected function is_html($file) {
        return substr($file,-5)=='.html';
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_FILES) && !Permission::check(Permission::TO_VIEW_IMAGES)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'order'=>$this->order,
                'root'=>UPLOADS_DIR
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root_dir = ROOT_DIR;
        $default_root = UPLOADS_DIR;
        $root = Zira\Request::post('root');
        if (empty($root) || strpos($root, $default_root)!==0 || strpos($root,'..')!==false) $root = $default_root;
        $items = scandir($root_dir . DIRECTORY_SEPARATOR . $root, $this->order=='asc' ? SCANDIR_SORT_ASCENDING : SCANDIR_SORT_DESCENDING);
        $folders = array();
        $files = array();
        foreach ($items as $item) {
            if ($item=='.' || $item=='..') continue;
            if ($root==$default_root && $item==self::THUMBS_FOLDER) continue;
            if ($root==$default_root && in_array($item, $this->_hidden_folders)) continue;
            if (is_dir($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $item)) $folders[]=$item;
            else $files[]=$item;
        }
        $files = array_merge($folders,$files);
        $this->total = count($files);
        $this->pages = ceil($this->total/$this->limit);
        if ($this->page>$this->pages) $this->page = $this->pages;
        if ($this->page<1) $this->page=1;
        $files = array_slice($files,$this->limit*($this->page-1), $this->limit);
        $bodyItems = array();
        foreach($files as $file) {
            if (!is_readable($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $file)) continue;
            $fsize = filesize($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $file);
            $fsize = number_format($fsize / 1024, 2). ' kB';
            if (is_dir($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $file)) {
                $mtime = date(Zira\Config::get('date_format'), filemtime($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $file));
                $bodyItems[]=$this->createBodyFolderItem($file, $file, $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'folder', 'parent'=>'files'), $mtime);
            } else if (($size=$this->get_image_size($root_dir . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $file))!=false) {
                $bodyItems[]=$this->createBodyItem($file, $file, Zira\Helper::baseUrl(str_replace(DIRECTORY_SEPARATOR, '/', $this->get_image_thumb($root . DIRECTORY_SEPARATOR . $file))), $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'image', 'parent'=>'files', 'image_width'=>$size[0], 'image_height'=>$size[1], 'image_url'=>Zira\Helper::baseUrl(str_replace(DIRECTORY_SEPARATOR,'/',$root) . '/' . $file)), $fsize);
            } else if (Permission::check(Permission::TO_VIEW_FILES)) {
                if ($this->is_archive($file)) {
                    $bodyItems[]=$this->createBodyArchiveItem($file, $file, $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'archive', 'parent'=>'files'), $fsize);
                } else if ($this->is_txt($file)) {
                    $bodyItems[]=$this->createBodyFileItem($file, $file, $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'txt', 'parent'=>'files'), $fsize);
                } else if ($this->is_html($file)) {
                    $bodyItems[]=$this->createBodyFileItem($file, $file, $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'html', 'parent'=>'files'), $fsize);
                } else {
                    $bodyItems[]=$this->createBodyFileItem($file, $file, $root . DIRECTORY_SEPARATOR . $file, $this->get_body_item_callback_js(), false, array('type'=>'file', 'parent'=>'files'), $fsize);
                }
            }
        }
        $this->setBodyItems($bodyItems);
        $this->setData(array(
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
            'root'=>$root
        ));
        $this->setTitle($root);
    }
}