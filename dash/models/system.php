<?php
/**
 * Zira project.
 * system.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Dash;
use Zira\Permission;

class System extends Model {
    public function dump() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) return;

        $installed_tables = Zira\Db\Db::getTables();
        $available_modules = Dash\Windows\Modules::getAvailableModules(false);

        echo '-- Zira dump '."\r\n";
        echo '-- '.date('Y-m-d H:i:s').' '."\r\n\r\n";

        foreach ($available_modules as $key=>$name) {
            $tables = Dash\Windows\Modules::getAvailableModuleTables($key);
            $installed = Dash\Windows\Modules::isModuleTablesInstalled($tables, $installed_tables);
            if (!$installed) continue;

            foreach($tables as $table) {
                $create_sql = $table->__toString();
                if (empty($create_sql)) continue;
                echo '-- Table '.$table->getName()."\r\n";
                echo $create_sql.";\r\n\r\n";
                $table->dump("\r\n", 1000, true);
                echo "\r\n\r\n";
                flush();
            }
        }

        echo '-- End of dump '."\r\n";
    }

    public function tree() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $tree = array();

        $stack = array(ROOT_DIR);
        while(count($stack)>0) {
            $d = array_shift($stack);
            $files = scandir($d);
            foreach($files as $file) {
                if ($file=='.' || $file=='..') continue;
                if (substr($file,0,1)=='.' && !is_writable($d . DIRECTORY_SEPARATOR . $file)) continue;
                if ($d . DIRECTORY_SEPARATOR . $file == ROOT_DIR . DIRECTORY_SEPARATOR . CACHE_DIR) continue;
                if ($d . DIRECTORY_SEPARATOR . $file == ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR) continue;
                if ($d . DIRECTORY_SEPARATOR . $file == ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR) continue;
                if (is_dir($d . DIRECTORY_SEPARATOR . $file)) {
                    $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                    continue;
                }
                $tree []= '[' . date('Y-m-d H:i:s', filemtime($d . DIRECTORY_SEPARATOR . $file)) . '] ' . substr($d . DIRECTORY_SEPARATOR . $file, strlen(ROOT_DIR . DIRECTORY_SEPARATOR));
            }
        }

        rsort($tree);

        return $tree;
    }

    public function cache() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        if (!Zira\Cache::clear(true)) return array('error' => Zira\Locale::t('An error occurred'));

        return array('message'=>Zira\Locale::t('Cache cleared'),'reload' => $this->getJSClassName());
    }
}