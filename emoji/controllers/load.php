<?php
/**
 * Zira project.
 * load.php
 * (c)2016 http://dro1d.ru
 */

namespace Emoji\Controllers;

use Emoji\Models\Emoji;
use Zira;

class Load extends Zira\Controller {
    public function typo() {
        if (!Zira\Request::isPost()) return;
        $typo = Zira\Request::post('typo');
        if (empty($typo)) return;
        echo Emoji::render($typo);
    }
}