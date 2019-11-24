<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// creating tags table
$recordTags = new \Zira\Install\Tag();
$recordTags->install();
Zira\Log::write('Created tags table');

// altering records table
$alterRecords = new \Update\V6\Record();
$alterRecords->execute();
Zira\Log::write('Updated records table');
