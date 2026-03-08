<?php
// This migration adds a "reviewed_until" column to the "settings" table
// to store the waterline date for subscription review tracking.

$columnQuery = $db->query("SELECT * FROM pragma_table_info('settings') WHERE name='reviewed_until'");
$columnRequired = $columnQuery->fetchArray(SQLITE3_ASSOC) === false;

if ($columnRequired) {
    $db->exec("ALTER TABLE settings ADD COLUMN reviewed_until DATE DEFAULT NULL");
}
