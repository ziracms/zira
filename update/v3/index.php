<?php
if (!defined('ZIRA_UPDATE') || !ZIRA_UPDATE) exit;

// adding new fields to widgets table
$alterWidgets = new \Update\V3\Widget();
$alterWidgets->execute();
Zira\Log::write('Updated widgets table');

// adding new fields to users table
$alterUsers = new \Update\V3\User();
$alterUsers->execute();
Zira\Log::write('Updated users table');

$alterForumCategories = new \Update\V3\Forumcategory();
if ($alterForumCategories->execute()) {
    Zira\Log::write('Updated forum categories table');
}

$alterForumForums = new \Update\V3\Forumforum();
if ($alterForumForums->execute()) {
    Zira\Log::write('Updated forum forums table');
}

$alterForumTopics = new \Update\V3\Forumtopic();
if ($alterForumTopics->execute()) {
    Zira\Log::write('Updated forum topics table');
}