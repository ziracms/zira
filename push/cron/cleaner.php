<?php
/**
 * Zira project.
 * cleaner.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Cron;

use Zira;

class Cleaner implements Zira\Cron {
    public function run() {
        \Push\Models\Subscription::cleanUp();
        return Zira\Locale::tm('Push subscription database cleaned up','push');
    }
}