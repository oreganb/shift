<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

$name = $_GET['name'] ?? '';
$ontology = shift_ontology();
$cls = $ontology['classes'][$name] ?? null;

if (!$cls) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

echo json_encode($cls, JSON_UNESCAPED_UNICODE);
