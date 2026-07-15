#!/usr/bin/env php
<?php
require_once dirname(__DIR__) . '/includes/config.php';

$ontology = json_decode(file_get_contents(SHIFT_JSON . '/ontology.json'), true);
$vault = json_decode(file_get_contents(SHIFT_JSON . '/vault.json'), true);

$index = [];

foreach ($ontology['classes'] ?? [] as $c) {
    $index[] = [
        'kind'       => 'class',
        'name'       => $c['name'],
        'label'      => $c['label'],
        'iri'        => $c['iri'],
        'definition' => $c['comment'] ?? '',
        'url'        => 'ontology-class.php?name=' . urlencode($c['name']),
    ];
}

foreach (array_merge($ontology['objectProperties'] ?? [], $ontology['datatypeProperties'] ?? []) as $p) {
    $index[] = [
        'kind'       => 'property',
        'name'       => $p['name'],
        'label'      => $p['label'],
        'iri'        => $p['iri'],
        'definition' => $p['comment'] ?? '',
        'url'        => 'ontology-property.php?name=' . urlencode($p['name']),
    ];
}

foreach ($ontology['rules'] ?? [] as $r) {
    $index[] = [
        'kind'       => 'rule',
        'name'       => $r['id'],
        'label'      => $r['label'],
        'iri'        => $r['iri'],
        'definition' => $r['comment'] ?? '',
        'url'        => 'rule.php?id=' . urlencode($r['id']),
    ];
}

foreach ($vault['notes'] ?? [] as $slug => $n) {
    $index[] = [
        'kind'       => 'note',
        'name'       => $slug,
        'label'      => $n['title'],
        'aliases'    => $n['meta']['aliases'] ?? [],
        'definition' => $n['preview'] ?? '',
        'url'        => 'graph.php?note=' . urlencode($slug),
    ];
}

file_put_contents(
    SHIFT_JSON . '/search-index.json',
    json_encode(['generated' => date('c'), 'items' => $index], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo 'Wrote ' . count($index) . " search items\n";
