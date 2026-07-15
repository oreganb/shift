#!/usr/bin/env php
<?php
require_once dirname(__DIR__) . '/includes/import-data.php';

try {
    $result = shift_import_to_db();
    echo 'Imported ' . $result['classes'] . ' classes, ' . $result['notes'] . " notes.\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
