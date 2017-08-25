<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// adding new permissions
$alterPermission = new \Update\V1\Permission();
$alterPermission->execute();
Zira\Log::write('Updated permissions table');

// adding new fields to category table
$alterCategory = new \Update\V1\Category();
$alterCategory->execute();
Zira\Log::write('Updated category table');

// adding new fields to record table
$alterRecord = new \Update\V1\Record();
$alterRecord->execute();
Zira\Log::write('Updated record table');

// creating files table
$filesTable = new \Zira\Install\File();
$filesTable->install();
Zira\Log::write('Created files table');

// creating audio table
$audioTable = new \Zira\Install\Audio();
$audioTable->install();
Zira\Log::write('Created audio table');

// creating videos table
$videosTable = new \Zira\Install\Video();
$videosTable->install();
Zira\Log::write('Created videos table');