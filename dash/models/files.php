<?php
/**
 * Zira project.
 * files.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Files extends Model {
    public function info($file, $dir) {
        if (!Permission::check(Permission::TO_VIEW_FILES) && !Permission::check(Permission::TO_VIEW_IMAGES)) {
            return array();
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        $dir = trim($dir, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file, UPLOADS_DIR)!==0) return array();
        if (empty($dir) || strpos($dir,'..')!==false || strpos($dir, UPLOADS_DIR)!==0) return array();
        if (strpos($file,$dir.DIRECTORY_SEPARATOR)!==0) return array();
        $p = strrpos($file, DIRECTORY_SEPARATOR);
        if (!$p) return array();
        $file = substr($file,$p+1);
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file)) return array();
        if (!is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file)) return array();
        $is_dir = is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file);
        if (!$is_dir) {
            $size=$this->getWindow()->get_image_size(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file);
            if (!$size && !Permission::check(Permission::TO_VIEW_FILES)) return array();
        } else {
            $size = false;
        }
        $info = array();
        $info[]='<span class="glyphicon glyphicon-tag"></span> '.Zira\Helper::html($file);
        $info[]='<span class="glyphicon glyphicon-time"></span> '.date(Zira\Config::get('date_format'),filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file));
        if (!$is_dir) {
            $info[]='<span class="glyphicon glyphicon-hdd"></span> '.number_format(filesize(ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file) / 1048576, 2).' MB';
        }
        if ($size!=false) {
            $info[]='<span class="glyphicon glyphicon-picture"></span> '.$size[0].'x'.$size[1].'px';
        }
        return $info;
    }

    public function upload() {
        if (!Permission::check(Permission::TO_UPLOAD_FILES) && !Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $response = array();
        $form = new Dash\Forms\Upload();
        if ($form->isValid()) {
            $files = $form->getValue('files');
            $dir = trim($form->getValue('dirroot'));
            $dir = trim($dir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            if (strpos($dir, UPLOADS_DIR.DIRECTORY_SEPARATOR)===0) {
                $dir = substr($dir,mb_strlen(UPLOADS_DIR,CHARSET));
            }
            $dir = trim($dir,DIRECTORY_SEPARATOR);
            if (!($saved=Zira\File::save($files, $dir))) {
                $response['error'] = Zira\Locale::t('An error occurred');
            } else {
                if (Zira\Config::get('watermark_enabled')) {
                    foreach ($saved as $path => $name) {
                        $p = strrpos($name, '.');
                        if ($p===false) continue;
                        $ext = substr($name, $p+1);
                        if (!in_array(strtolower($ext), array('jpg','jpeg','gif','png'))) continue;
                        $size = @getimagesize($path);
                        if (!$size) continue;
                        if (!Zira\Image::watermark($path)) break;
                    }
                }
                $response['reload'] = $this->getJSClassName();
            }
        } else {
            $response['error'] = $form->getError();
        }

        return $response;
    }

    public function mkdir($root, $name) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES) && !Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root = trim((string)$root,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($root, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($root,'..')!==false || strpos($root,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $root = trim($root,DIRECTORY_SEPARATOR);
        $name = trim(trim($name, DIRECTORY_SEPARATOR));
        if (empty($name) || strpos($name,'..')!==false || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (file_exists(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('File or directory with such name already exists'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $root) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $root)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        if (!@mkdir(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        return array('reload' => $this->getJSClassName());
    }

    public function createTextFile($root, $name) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root = trim((string)$root,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($root, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($root,'..')!==false || strpos($root,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $root = trim($root,DIRECTORY_SEPARATOR);
        $name = trim(trim($name, DIRECTORY_SEPARATOR));
        if (empty($name) || strpos($name,'..')!==false || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $name .= '.txt';
        if (file_exists(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('File or directory with such name already exists'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $root) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $root)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        if (!@touch(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        return array('reload' => $this->getJSClassName());
    }

    public function createHTMLFile($root, $name) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root = trim((string)$root,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($root, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($root,'..')!==false || strpos($root,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $root = trim($root,DIRECTORY_SEPARATOR);
        $name = trim(trim($name, DIRECTORY_SEPARATOR));
        if (empty($name) || strpos($name,'..')!==false || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $name .= '.html';
        if (file_exists(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('File or directory with such name already exists'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $root) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $root)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        if (!@touch(ROOT_DIR.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$name)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        return array('reload' => $this->getJSClassName());
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array();
        if (!Permission::check(Permission::TO_DELETE_FILES) && !Permission::check(Permission::TO_DELETE_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $dirs = array();
        $files = array();
        foreach($data as $item) {
            $item = trim((string)$item,DIRECTORY_SEPARATOR);
            if (strpos($item,'..')!==false || strpos($item,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            if ($item==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . $item;
            if (!file_exists($path)) return array('error' => Zira\Locale::t('An error occurred'));
            if (is_dir($path)) {
                $dirs[]=$path;
                $stack = array($item);
                while(count($stack)>0) {
                    $d = array_shift($stack);
                    $_files = scandir(ROOT_DIR . DIRECTORY_SEPARATOR . $d);
                    foreach($_files as $file) {
                        if ($file=='.' || $file=='..') continue;
                        if (is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file)) {
                            $dirs[]=ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file;
                            $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                            continue;
                        }
                        $s = $this->getWindow()->get_image_size(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file);
                        if (!$s && !Permission::check(Permission::TO_DELETE_FILES)) return array('error'=>Zira\Locale::t('Permission denied'));
                        $files[]=ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file;
                        if ($s) {
                            $p = strpos($d,'/');
                            if ($p!==false) $_path = substr($d, $p+1);
                            else $_path = '';
                            $_path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_path . DIRECTORY_SEPARATOR . $file;
                            if (file_exists($_path) && !is_dir($_path)) {
                                $files[]=$_path;
                            }
                        }
                    }
                }
            } else {
                $s = $this->getWindow()->get_image_size($path);
                if (!$s && !Permission::check(Permission::TO_DELETE_FILES)) return array('error'=>Zira\Locale::t('Permission denied'));
                $files[]=$path;
                if ($s) {
                    $p = strpos($item,'/');
                    if ($p!==false) $_path = substr($item, $p+1);
                    else $_path = '';
                    $_path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_path;
                    if (file_exists($_path) && !is_dir($_path)) {
                        $files[]=$_path;
                    }
                }
            }
        }
        $dirs=array_reverse($dirs);
        $paths = array_merge($files, $dirs);
        foreach($paths as $path) {
            if (is_dir($path)) {
                if (!@rmdir($path)) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                if (!@unlink($path)) return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        return array('reload' => $this->getJSClassName());
    }

    public function rename($file, $name) {
        if (!Permission::check(Permission::TO_DELETE_FILES) && !Permission::check(Permission::TO_DELETE_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim((string)$file,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($file, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $file = trim($file,DIRECTORY_SEPARATOR);
        if ($file==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
        $name = trim(trim($name, DIRECTORY_SEPARATOR));
        if (empty($name) || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (substr($name,-4)=='.php') {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        $root = dirname($path);
        if (!file_exists($path) || !is_readable($path)) return array('error' => Zira\Locale::t('An error occurred'));
        $s = $this->getWindow()->get_image_size($path);
        if (!$s && !Permission::check(Permission::TO_DELETE_FILES)) return array('error'=>Zira\Locale::t('Permission denied'));
        $new = $root.DIRECTORY_SEPARATOR.$name;
        if (file_exists($new)) {
            return array('error' => Zira\Locale::t('File or directory with such name already exists'));
        }
        if (is_dir($path) && !Permission::check(Permission::TO_DELETE_FILES)) return array('error'=>Zira\Locale::t('Permission denied'));
        if (!@rename($path, $new)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $p = strpos($file,DIRECTORY_SEPARATOR);
        if ($p!==false) $_path = substr($file, $p+1);
        else $_path = '';
        $_path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_path;
        if (file_exists($_path) && !is_dir($_path)) {
            @unlink($_path);
        } else if (file_exists($_path) && is_dir($_path)) {
            $stack = array($_path);
            while(count($stack)>0) {
                $d = array_shift($stack);
                $files = scandir($d);
                foreach($files as $file) {
                    if ($file=='.' || $file=='..') continue;
                    if (is_dir($d . DIRECTORY_SEPARATOR . $file)) {
                        $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                        continue;
                    }
                    @unlink($d . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        return array('reload' => $this->getJSClassName());
    }

    public function copy($file, $dir) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES) && !Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim((string)$file,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($file, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $file = trim($file,DIRECTORY_SEPARATOR);
        if ($file==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
        $dir = trim($dir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($dir, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($dir,'..')!==false || strpos($dir,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $dir = trim($dir,DIRECTORY_SEPARATOR);
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        $root = ROOT_DIR . DIRECTORY_SEPARATOR . $dir;
        if (!file_exists($path) || !is_readable($path)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!file_exists($root) || !is_dir($root)) return array('error' => Zira\Locale::t('Folder not found'));
        if ($root==$path || strpos($root, $path.DIRECTORY_SEPARATOR)===0)  return array('error' => Zira\Locale::t('An error occurred'));
        $name = basename($file);
        $new = $root.DIRECTORY_SEPARATOR.$name;
        $i=0;
        while (file_exists($new)) {
            $i++;
            $p=strrpos($name,'.');
            if ($p!==false) {
                $new = $root.DIRECTORY_SEPARATOR.substr($name,0,$p).'-'.$i.substr($name,$p);
            } else {
                $new = $root.DIRECTORY_SEPARATOR.$name.'-'.$i;
            }
        }
        if (!is_dir($path)) {
            $s = $this->getWindow()->get_image_size($path);
            if (!$s && !Permission::check(Permission::TO_UPLOAD_FILES)) return array('error'=>Zira\Locale::t('Permission denied'));
            if (!@copy($path, $new)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        } else {
            @mkdir($new);
            $stack = array($file);
            $dst = array($file=>$new);
            while(count($stack)>0) {
                $d = array_shift($stack);
                $_d = $dst[$d];
                $files = scandir(ROOT_DIR . DIRECTORY_SEPARATOR . $d);
                foreach($files as $file) {
                    if ($file=='.' || $file=='..') continue;
                    if (is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file)) {
                        $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                        $dst[$d . DIRECTORY_SEPARATOR . $file] = $_d . DIRECTORY_SEPARATOR . $file;
                        @mkdir($_d . DIRECTORY_SEPARATOR . $file);
                        continue;
                    }
                    $s = $this->getWindow()->get_image_size(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file);
                    if (!$s && !Permission::check(Permission::TO_UPLOAD_FILES)) continue;
                    @copy(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file, $_d . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        return array('reload' => $this->getJSClassName());
    }

    public function move($file, $dir) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES) && !Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim((string)$file,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($file, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $file = trim($file,DIRECTORY_SEPARATOR);
        if ($file==UPLOADS_DIR) return array('error' => Zira\Locale::t('An error occurred'));
        $dir = trim($dir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($dir, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($dir,'..')!==false || strpos($dir,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $dir = trim($dir,DIRECTORY_SEPARATOR);
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        $root = ROOT_DIR . DIRECTORY_SEPARATOR . $dir;
        if (!file_exists($path) || !is_readable($path)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!file_exists($root) || !is_dir($root)) return array('error' => Zira\Locale::t('Folder not found'));
        if ($root==$path || strpos($root, $path.DIRECTORY_SEPARATOR)===0)  return array('error' => Zira\Locale::t('An error occurred'));
        $name = basename($file);
        $new = $root.DIRECTORY_SEPARATOR.$name;
        if (file_exists($new)) return array('error' => Zira\Locale::t('File or directory already exists'));
        if (!@rename($path, $new)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $p = strpos($file,DIRECTORY_SEPARATOR);
        if ($p!==false) $_path = substr($file, $p+1);
        else $_path = '';
        $_path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_path;
        if (file_exists($_path) && !is_dir($_path)) {
            @unlink($_path);
        } else if (file_exists($_path) && is_dir($_path)) {
            $stack = array($_path);
            while(count($stack)>0) {
                $d = array_shift($stack);
                $files = scandir($d);
                foreach($files as $file) {
                    if ($file=='.' || $file=='..') continue;
                    if (is_dir($d . DIRECTORY_SEPARATOR . $file)) {
                        $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                        continue;
                    }
                    @unlink($d . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        return array('reload' => $this->getJSClassName());
    }

    public function pack($name, $items, $root) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root = trim((string)$root,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($root, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($root,'..')!==false || strpos($root,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $root = trim($root,DIRECTORY_SEPARATOR);
        if (empty($name) || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $root) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $root)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        $name .= '.zip';
        $zip = new \ZipArchive();
        if ($zip->open(ROOT_DIR . DIRECTORY_SEPARATOR . $root . DIRECTORY_SEPARATOR . $name, \ZipArchive::CREATE)!==TRUE) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        foreach($items as $item) {
            if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $item) || !is_readable(ROOT_DIR . DIRECTORY_SEPARATOR . $item)) continue;
            if (strpos($item,$root.DIRECTORY_SEPARATOR)!==0 || strpos($item, '..')!==false) continue;
            if (is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $item)) {
                $name = $item;
                $p=mb_strlen($root, CHARSET);
                $name = mb_substr($name, $p+1, null, CHARSET);
                $zip->addEmptyDir($name);
                $stack = array($item);
                while(count($stack)>0) {
                    $d = array_shift($stack);
                    $files = scandir(ROOT_DIR . DIRECTORY_SEPARATOR . $d);
                    foreach($files as $file) {
                        if ($file=='.' || $file=='..') continue;
                        $name = $d . DIRECTORY_SEPARATOR . $file;
                        $p=mb_strlen($root, CHARSET);
                        $name = mb_substr($name, $p+1, null, CHARSET);
                        if (is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file)) {
                            $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                            $zip->addEmptyDir($name);
                            continue;
                        }
                        $zip->addFile(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file, $name);
                    }
                }
            } else {
                $name = $item;
                $p=mb_strlen($root, CHARSET);
                $name = mb_substr($name, $p+1, null, CHARSET);
                $zip->addFile(ROOT_DIR . DIRECTORY_SEPARATOR . $item, $name);
            }
        }
        $numFiles = $zip->numFiles;
        $zip->close();
        return array('message'=>Zira\Locale::t('Added %s files', $numFiles), 'reload'=>$this->getJSClassName());
    }

    public function unpack($file, $root) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $root = trim((string)$root,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($root, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($root,'..')!==false || strpos($root,UPLOADS_DIR.DIRECTORY_SEPARATOR.Dash\Windows\Files::THUMBS_FOLDER)===0) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $root = trim($root,DIRECTORY_SEPARATOR);
        $file = trim($file,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (strpos($file, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 || strpos($file,'..')!==false) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $file = trim($file,DIRECTORY_SEPARATOR);
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path) || is_dir($path)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $root) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $root)) {
            return array('error' => Zira\Locale::t('Folder not found'));
        }
        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE)!==TRUE) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        $zip->extractTo(ROOT_DIR . DIRECTORY_SEPARATOR . $root);
        $zip->close();
        return array('reload'=>$this->getJSClassName());
    }
}