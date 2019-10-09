<?php
/**
 * Zira project.
 * cleaner.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Cron;

use Zira;

class Cleaner implements Zira\Cron {
    public function run() {
        Zira\Models\Captcha::cleanUp();
        Zira\Models\Draft::cleanUp();
        return Zira\Locale::t('Database cleaned up');
    }
}