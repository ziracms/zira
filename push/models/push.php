<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Models;

use Zira;
use Dash;
use Zira\Permission;

class Push extends Dash\Models\Model {
    public function generateKeys() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $keys = \Push\Push::createVapidKeys();
        return $keys;
    }
}