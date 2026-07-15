<?php
/**
 * SHIFT Ontology Website — bootstrap
 * Run build scripts after updating OWL or vault sources.
 */
require_once __DIR__ . '/includes/config.php';

function shift_load_json(string $file): array {
    $path = SHIFT_JSON . '/' . $file;
    return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
}

echo "SHIFT Ontology Website\n";
echo "======================\n\n";
echo "1. Build JSON:  php scripts/build-ontology-json.php && php scripts/build-vault-json.php && php scripts/build-search-index.php\n";
echo "2. Setup MySQL: mysql -u root < scripts/schema.sql\n";
echo "3. Import DB:   php scripts/import-db.php  (optional — site works from JSON)\n";
echo "4. Serve:       php -S localhost:8080 -t .\n\n";
echo "Version: " . SHIFT_VERSION . "\n";
echo "Classes: " . (shift_load_json('ontology.json')['stats']['classes'] ?? '?') . "\n";
