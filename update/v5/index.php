<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// adding new fields to chats table
$alterChats = new \Update\V5\Chat();
$alterChats->execute();
Zira\Log::write('Updated chats table');

// altering records table
$alterRecords = new \Update\V5\Record();
$alterRecords->execute();
Zira\Log::write('Updated records table');