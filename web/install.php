<?php
/**
 * One-time server setup: create tables and import JSON into MySQL.
 * DELETE THIS FILE after successful installation.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/import-data.php';

$installKey = 'shift-install-' . substr(md5(SHIFT_VERSION . 'tyndall'), 0, 12);

if (($_GET['key'] ?? '') !== $installKey) {
    http_response_code(403);
    die('Forbidden. Set ?key=' . $installKey);
}

header('Content-Type: text/plain; charset=utf-8');
echo "SHIFT install — " . SHIFT_VERSION . "\n\n";

try {
    $db = shift_db();
    $db->exec('SET NAMES utf8mb4');
} catch (Throwable $e) {
    die('DB connection failed: ' . $e->getMessage() . "\n");
}

$sql = preg_replace('/--.*$/m', '', file_get_contents(__DIR__ . '/scripts/schema.sql'));
foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
    if ($stmt === '') {
        continue;
    }
    try {
        $db->exec($stmt);
        echo 'OK: ' . substr($stmt, 0, 60) . "…\n";
    } catch (Throwable $e) {
        echo 'SKIP: ' . $e->getMessage() . "\n";
    }
}

$tables = ['ontology_classes', 'ontology_properties', 'reasoning_rules', 'vault_notes', 'vault_links', 'site_metadata'];
foreach ($tables as $table) {
    try {
        $db->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "UTF8MB4: $table\n";
    } catch (Throwable $e) {
        echo "UTF8MB4 skip $table: " . $e->getMessage() . "\n";
    }
}

echo "\nImporting data…\n";
try {
    $result = shift_import_to_db();
    echo 'Imported ' . $result['classes'] . ' classes, ' . $result['notes'] . " notes.\n";
    echo "\nDone. DELETE install.php now.\n";
} catch (Throwable $e) {
    echo 'Import failed: ' . $e->getMessage() . "\n";
}
