<?php
/**
 * Zira project.
 * files.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Models;

use Zira;
use Dash;
use Forum;
use Zira\Permission;

class Files extends Dash\Models\Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Forum\Forum::PERMISSION_MODERATE)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $item) {
            $parts = explode('_', $item);
            if (count($parts)!=2) return array('error' => Zira\Locale::t('An error occurred'));
            $id = intval($parts[0]);
            $field_num = intval($parts[1]);
            if ($field_num < 1 || $field_num > Forum\Models\File::MAX_FILES_COUNT) return array('error' => Zira\Locale::t('An error occurred'));

            $row = new Forum\Models\File($id);
            if (!$row->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

            $path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $row->{'path'.$field_num};
            if (file_exists($path)) @unlink($path);

            $void = $field_num > 1 ? null : '';
            $row->{'path'.$field_num} = $void;

            $exists = false;
            for ($i=1; $i<=\Forum\Models\File::MAX_FILES_COUNT; $i++) {
                if ($row->{'path'.$i}) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) $row->delete();
            else $row->save();
        }

        return array('reload' => $this->getJSClassName());
    }
}