<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// adding new fields to widgets table
$alterWidgets = new \Update\V3\Widget();
$alterWidgets->execute();
Zira\Log::write('Updated widgets table');
