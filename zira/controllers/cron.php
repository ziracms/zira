<?php
/**
 * Zira project.
 * cron.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Controllers;

use Zira;

class Cron extends Zira\Controller {
    /**
     * Cron tasks action
     */
    public function index() {
        $output = '';
        $last = Zira\Config::get('cron_run');
        if ($last && time()-$last<CRON_MIN_INTERVAL) {
            $output .= Zira\Locale::t('Cron executed less than an minute ago');
        } else {
            $sys_modules = array('zira');
            $config_modules = Zira\Config::get('modules');
            $modules = array_merge($sys_modules, $config_modules);

            foreach ($modules as $module) {
                $tasks = Zira::getModuleCronTasks($module);
                if (empty($tasks)) continue;
                $output .= '[' . $module . ']' . "\r\n";
                $co = 0;
                foreach ($tasks as $task) {
                    $co++;
                    try {
                        $response = $task->run();
                    } catch (\Exception $e) {
                        $response = Zira\Locale::t('An error occurred') . ': ' . $e->getMessage();
                        Zira\Log::exception($e);
                    }
                    $output .= "\t" . $response . "\r\n";
                }
            }
            Zira\Models\Option::write('cron_run', time());
        }
        if (Zira\View::isAjax()) {
            echo json_encode(explode("\r\n",$output));
        } else {
            header('Content-Type: text/plain; charset='.CHARSET);
            echo $output;
        }
    }
}