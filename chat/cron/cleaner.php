<?php
/**
 * Zira project.
 * cleaner.php
 * (c)2017 https://github.com/ziracms/zira
 */

namespace Chat\Cron;

use Zira;

class Cleaner implements Zira\Cron {
    public function run() {
        \Chat\Models\Message::cleanUp();
        return Zira\Locale::tm('Chat database cleaned up','chat');
    }
}