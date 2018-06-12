<?php
/**
 * Zira project.
 * cleaner.php
 * (c)2018 http://dro1d.ru
 */

namespace Stat\Cron;

use Zira;

class Cleaner implements Zira\Cron {
    public function run() {
        \Stat\Models\Access::cleanUp();
        \Stat\Models\Visitor::cleanUp();
        \Stat\Models\Agent::cleanUp();
        return Zira\Locale::tm('Statistics cleaned up','stat');
    }
}