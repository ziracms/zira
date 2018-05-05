<?php
/**
 * Zira project.
 * usermenupopup.php
 * (c)2018 http://dro1d.ru
 */

namespace Zira\Widgets;

use Zira;

class Usermenupopup extends Usermenu {
    protected $_title = 'User menu with popup window';
    protected $_authorizied_class = 'authorized';
    protected $_unauthorizied_class = 'not-authorized usermenu-popup';
}