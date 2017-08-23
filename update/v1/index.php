<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

$alterPermission = new \Update\V1\Permission();
$alterPermission->execute();