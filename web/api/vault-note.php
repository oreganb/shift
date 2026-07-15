<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

$slug = $_GET['slug'] ?? '';
$vault = shift_vault();
$note = $vault['notes'][$slug] ?? null;

if (!$note) {
    http_response_code(404);
    echo json_encode(['error' => 'Note not found']);
    exit;
}

// Render markdown server-side for AJAX
require_once dirname(__DIR__) . '/includes/helpers.php';
$html = shift_render_markdown($note['body']);

echo json_encode([
    'slug'      => $slug,
    'title'     => $note['title'],
    'meta'      => $note['meta'],
    'html'      => $html,
    'backlinks' => $note['backlinks'],
    'links'     => $note['links'],
    'folder'    => $note['folder'],
], JSON_UNESCAPED_UNICODE);
