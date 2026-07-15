<?php
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

$q = trim($_GET['q'] ?? '');
$limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));

if ($q === '') {
    echo json_encode(['results' => []]);
    exit;
}

$results = [];
$qLower = strtolower($q);

// Primary: JSON search index (always available on server)
try {
    $index = shift_search_index();
    foreach ($index['items'] ?? [] as $item) {
        $hay = strtolower(
            ($item['label'] ?? '') . ' ' .
            ($item['name'] ?? '') . ' ' .
            ($item['definition'] ?? '') . ' ' .
            implode(' ', $item['aliases'] ?? [])
        );
        if (str_contains($hay, $qLower)) {
            $pos = strpos($hay, $qLower);
            $item['score'] = $pos === false ? 0 : 100 - min(99, $pos);
            $results[] = $item;
        }
    }
    usort($results, function ($a, $b) {
        return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
    });
    $results = array_slice($results, 0, $limit);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search index unavailable', 'results' => []]);
    exit;
}

// Optional: enrich with MySQL full-text note hits when DB is configured
if (file_exists(dirname(__DIR__) . '/includes/config.local.php')) {
    require_once dirname(__DIR__) . '/includes/db.php';
    if (shift_db_available()) {
        try {
            $db = shift_db();
            $db->exec('SET NAMES utf8mb4');
            $stmt = $db->query(
                'SELECT slug, title, preview FROM vault_notes
                 WHERE title LIKE ' . $db->quote('%' . $q . '%') . '
                    OR preview LIKE ' . $db->quote('%' . $q . '%') . '
                 LIMIT ' . (int) $limit
            );
            $seen = array_column($results, 'url');
            foreach ($stmt->fetchAll() as $row) {
                $url = 'graph.php?note=' . urlencode($row['slug']);
                if (in_array($url, $seen, true)) {
                    continue;
                }
                array_unshift($results, [
                    'kind'       => 'note',
                    'label'      => $row['title'],
                    'definition' => substr($row['preview'] ?? '', 0, 120),
                    'url'        => $url,
                    'score'      => 110,
                ]);
            }
            $results = array_slice($results, 0, $limit);
        } catch (Throwable $e) {
            // JSON results already populated
        }
    }
}

echo json_encode(['query' => $q, 'results' => $results], JSON_UNESCAPED_UNICODE);
