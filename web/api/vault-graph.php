<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

$focus = $_GET['focus'] ?? '';
$depth = min(2, max(1, (int)($_GET['depth'] ?? 1)));

$vault = shift_vault();
$graph = $vault['graph'] ?? ['nodes' => [], 'edges' => []];

if ($focus !== '') {
    // Filter to neighbourhood
    $neighbours = [$focus];
    foreach ($graph['edges'] as $e) {
        if ($e['source'] === $focus) $neighbours[] = $e['target'];
        if ($e['target'] === $focus) $neighbours[] = $e['source'];
    }
    $neighbours = array_unique($neighbours);
    $nodes = array_values(array_filter($graph['nodes'], function ($n) use ($neighbours) {
        return in_array($n['id'], $neighbours, true);
    }));
    $edges = array_values(array_filter($graph['edges'], function ($e) use ($neighbours) {
        return in_array($e['source'], $neighbours, true) && in_array($e['target'], $neighbours, true);
    }));
    echo json_encode(['nodes' => $nodes, 'edges' => $edges, 'focus' => $focus]);
    exit;
}

echo json_encode($graph, JSON_UNESCAPED_UNICODE);
