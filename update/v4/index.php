<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// adding new keys to records table
$alterRecords = new \Update\V4\Record();
$alterRecords->execute();
Zira\Log::write('Updated records table');

// adding new fields to slides table
$alterSlides = new \Update\V4\Slide();
$alterSlides->execute();
Zira\Log::write('Updated slides table');