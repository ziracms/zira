<?php
/**
 * Zira project.
 * image.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class Image extends Model {
    protected function create_tmp_file($file) {
        $tmp_file = $this->get_tmp_file($file);
        copy(ROOT_DIR . DIRECTORY_SEPARATOR . $file, $tmp_file);
    }

    protected function delete_tmp_file($file) {
        $tmp_file = $this->get_tmp_file($file);
        if (file_exists($tmp_file)) unlink($tmp_file);
    }

    protected function get_tmp_name($file) {
        $p = strrpos($file,'.');
        if ($p) {
            $ext = substr($file, $p);
        } else {
            $ext = '';
        }
        return 'image-'.md5($file).$ext;
    }

    protected function get_tmp_file($file) {
        $tmp_dir = Zira\File::getAbsolutePath(TMP_DIR);
        $name = $this->get_tmp_name($file);
        return $tmp_dir . DIRECTORY_SEPARATOR . $name;
    }

    protected function get_tmp_url($file) {
        $name = $this->get_tmp_name($file);
        return UPLOADS_DIR . '/' . TMP_DIR . '/' . $name . '?t='.time();
    }

    public function open($file) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $this->create_tmp_file($file);
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        return array(
            'width'=> $size[0],
            'height' => $size[1],
            'src' => $src
        );
    }

    public function close($file) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array();
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array();
        }

        $this->delete_tmp_file($file);
        return array();
    }

    public function changeWidth($file, $width) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $width = intval($width);
        if ($width<=0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        $height = floor($_size[1] * $width / $_size[0]);

        if (!Zira\Image::resize($tmp_file, $tmp_file, $width, $height)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        return array(
            'width' => $width,
            'height' => $height,
            'src' => $src
        );
    }

    public function changeHeight($file, $height) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $height = intval($height);
        if ($height<=0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        $width = floor($_size[0] * $height / $_size[1]);

        if (!Zira\Image::resize($tmp_file, $tmp_file, $width, $height)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        return array(
            'width' => $width,
            'height' => $height,
            'src' => $src
        );
    }

    public function cropWidth($file, $width) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $width = intval($width);
        if ($width<=0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        if ($width>$_size[0]) return array('error'=>Zira\Locale::t('An error occurred'));

        $height = $_size[1];

        if (!Zira\Image::cut($tmp_file, $tmp_file, $width, $height, 0, 0)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        return array(
            'width' => $width,
            'height' => $height,
            'src' => $src
        );
    }

    public function cropHeight($file, $height) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $height = intval($height);
        if ($height<=0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        if ($height>$_size[1]) return array('error'=>Zira\Locale::t('An error occurred'));

        $width = $_size[0];

        if (!Zira\Image::cut($tmp_file, $tmp_file, $width, $height, 0, 0)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        return array(
            'width' => $width,
            'height' => $height,
            'src' => $src
        );
    }

    public function crop($file, $width, $height, $left, $top) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!is_numeric($width) || !is_numeric($height) || !is_numeric($left) || !is_numeric($top)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if ($width>100 || $height>100 || $left>100 || $top>100) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        if (!Zira\Image::crop($tmp_file, $tmp_file, $width, $height, $left, $top)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        return array(
            'width' => $_size[0],
            'height' => $_size[1],
            'src' => $src
        );
    }

    public function saveTmpImage($file) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        if (!@copy($tmp_file, $path)) return array('error'=>Zira\Locale::t('An error occurred'));

        $p = strpos($file, DIRECTORY_SEPARATOR);
        if ($p!==false) {
            $_file = substr($file, $p+1);
            if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_file)) {
                @unlink(ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . Dash\Windows\Files::THUMBS_FOLDER . DIRECTORY_SEPARATOR . $_file);
            }
        }

        return array('message'=>Zira\Locale::t('Successfully saved'));
    }

    public function saveTmpImageAs($file, $name) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (empty($name) || strpos($name,'..')!==false || strpos($name,DIRECTORY_SEPARATOR)!==false) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        $p = strrpos($file, '.');
        if ($p!==false) {
            $ext = substr($file, $p);
        } else {
            $ext = '';
        }
        $name .= $ext;
        $p = strrpos($file, DIRECTORY_SEPARATOR);
        if ($p!==false) {
            $_file = substr($file, 0, $p);
        } else {
            $_file = $file;
        }
        $_path = ROOT_DIR . DIRECTORY_SEPARATOR . $_file . DIRECTORY_SEPARATOR . $name;
        if (file_exists($_path)) {
            return array('error'=>Zira\Locale::t('File or directory with such name already exists'));
        }

        if (!@copy($tmp_file, $_path)) return array('error'=>Zira\Locale::t('An error occurred'));

        return array('message'=>Zira\Locale::t('Successfully saved'));
    }

    public function watermark($file) {
        if (!Permission::check(Permission::TO_UPLOAD_IMAGES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $size = getimagesize($path);
        if (!$size) return array('error'=>Zira\Locale::t('An error occurred'));

        $tmp_file = $this->get_tmp_file($file);
        if (!file_exists($tmp_file))  return array('error'=>Zira\Locale::t('An error occurred'));

        if (!Zira\Image::watermark($tmp_file)) return array('error'=>Zira\Locale::t('An error occurred'));
        $src = Zira\Helper::baseUrl($this->get_tmp_url($file));

        $_size = getimagesize($tmp_file);
        if (!$_size) return array('error'=>Zira\Locale::t('An error occurred'));

        return array(
            'width' => $_size[0],
            'height' => $_size[1],
            'src' => $src
        );
    }
}