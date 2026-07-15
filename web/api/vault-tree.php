<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

$vault = shift_vault();
echo json_encode([
    'tree'  => $vault['tree'] ?? [],
    'stats' => ['notes' => $vault['noteCount'] ?? 0, 'edges' => $vault['edgeCount'] ?? 0],
], JSON_UNESCAPED_UNICODE);
