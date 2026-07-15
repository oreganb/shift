<?php
/**
 * SHIFT Ontology Website — configuration
 */

define('SHIFT_ROOT', dirname(__DIR__));
define('SHIFT_DATA', SHIFT_ROOT . '/data');
define('SHIFT_JSON', SHIFT_DATA . '/json');
define('SHIFT_VAULT', SHIFT_DATA . '/vault/SHIFT Ontology');
define('SHIFT_ONTOLOGY', SHIFT_DATA . '/ontology');
define('SHIFT_VERSION', '0.1.1');
define('SHIFT_IRI_BASE', 'http://shift-ontology.org/core#');

// Local overrides first (server credentials — never committed)
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Defaults when not set in config.local.php
if (!defined('DB_HOST')) define('DB_HOST', getenv('SHIFT_DB_HOST') ?: '127.0.0.1');
if (!defined('DB_NAME')) define('DB_NAME', getenv('SHIFT_DB_NAME') ?: 'shift_ontology');
if (!defined('DB_USER')) define('DB_USER', getenv('SHIFT_DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('SHIFT_DB_PASS') ?: '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
