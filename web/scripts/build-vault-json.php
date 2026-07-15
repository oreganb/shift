#!/usr/bin/env php
<?php
/**
 * Parse Obsidian vault markdown into data/json/vault.json
 */
require_once dirname(__DIR__) . '/includes/config.php';

$vaultRoot = SHIFT_VAULT;
if (!is_dir($vaultRoot)) {
    fwrite(STDERR, "ERROR: vault not found at $vaultRoot\n");
    exit(1);
}

function parseFrontmatter(string $content): array
{
    if (!preg_match('/^---\r?\n(.*?)\r?\n---\r?\n(.*)$/s', $content, $m)) {
        return ['meta' => [], 'body' => $content];
    }
    $meta = [];
    foreach (preg_split('/\r?\n/', $m[1]) as $line) {
        if (preg_match('/^(\w+):\s*(.+)$/', $line, $fm)) {
            $meta[$fm[1]] = trim($fm[2]);
        } elseif (preg_match('/^tags:\s*$/', $line)) {
            $meta['tags'] = [];
        } elseif (preg_match('/^\s*-\s*(.+)$/', $line, $tm) && isset($meta['tags'])) {
            $meta['tags'][] = trim($tm[1]);
        } elseif (preg_match('/^aliases:\s*$/', $line)) {
            $meta['aliases'] = [];
        } elseif (preg_match('/^\s*-\s*(.+)$/', $line, $am) && isset($meta['aliases'])) {
            $meta['aliases'][] = trim($am[1]);
        }
    }
    return ['meta' => $meta, 'body' => $m[2]];
}

function extractWikiLinks(string $body): array
{
    $links = [];
    if (preg_match_all('/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/', $body, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $links[] = ['target' => trim($m[1]), 'alias' => isset($m[2]) ? trim($m[2]) : null];
        }
    }
    return $links;
}

function folderColor(string $folder): string
{
    $map = [
        '00_Core'                  => '#1F3D7A',
        '01_Actors'                => '#162C5C',
        '02_Assets'                => '#4A9440',
        '03_Flexibility_Services'  => '#F5A623',
        '04_Trading_and_Contracts' => '#2E62B0',
        '05_Reasoning'             => '#3D4F7A',
        '06_Data'                  => '#2E62B0',
        '07_Platforms'             => '#1F3D7A',
        '08_Standards'             => '#6B7280',
        '09_Methodologies'         => '#4A9440',
    ];
    $top = explode('/', $folder)[0] ?? '';
    return $map[$top] ?? '#1F3D7A';
}

function buildTree(string $dir, string $relative = ''): array
{
    $tree = [];
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === '.obsidian') {
            continue;
        }
        $path = $dir . '/' . $item;
        $rel = $relative ? $relative . '/' . $item : $item;
        if (is_dir($path)) {
            $tree[] = [
                'type'     => 'folder',
                'name'     => $item,
                'path'     => $rel,
                'children' => buildTree($path, $rel),
            ];
        } elseif (str_ends_with($item, '.md') && $item !== '_Index.md') {
            $tree[] = [
                'type' => 'file',
                'name' => substr($item, 0, -3),
                'path' => $rel,
                'slug' => substr($rel, 0, -3),
            ];
        }
    }
    usort($tree, fn($a, $b) => strcmp($a['name'], $b['name']));
    return $tree;
}

$notes = [];
$unresolved = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($vaultRoot, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }
    $basename = $file->getBasename('.md');
    if ($basename === '_Index') {
        continue;
    }
    $fullPath = $file->getPathname();
    $relative = substr($fullPath, strlen($vaultRoot) + 1);
    $slug = substr($relative, 0, -3);
    $content = file_get_contents($fullPath);
    $parsed = parseFrontmatter($content);
    $links = extractWikiLinks($parsed['body']);
    $folder = dirname($slug);
    if ($folder === '.') {
        $folder = '';
    }

    $notes[$slug] = [
        'slug'      => $slug,
        'title'     => $basename,
        'folder'    => $folder,
        'color'     => folderColor($folder),
        'meta'      => $parsed['meta'],
        'body'      => $parsed['body'],
        'links'     => $links,
        'backlinks' => [],
        'preview'   => mb_substr(preg_replace('/[#*\[\]|`>-]/', '', strip_tags($parsed['body'])), 0, 200),
    ];
}

// Title index for link resolution
$titleIndex = [];
foreach ($notes as $slug => $note) {
    $titleIndex[$note['title']] = $slug;
    foreach ($note['meta']['aliases'] ?? [] as $alias) {
        $titleIndex[$alias] = $slug;
    }
}

// Resolve links and build backlinks
foreach ($notes as $slug => &$note) {
    foreach ($note['links'] as &$link) {
        $target = $link['target'];
        if (isset($titleIndex[$target])) {
            $link['resolved'] = $titleIndex[$target];
            $notes[$titleIndex[$target]]['backlinks'][] = [
                'from'    => $slug,
                'title'   => $note['title'],
                'context' => $note['preview'],
            ];
        } else {
            $link['resolved'] = null;
            $unresolved[] = ['from' => $slug, 'target' => $target];
        }
    }
}
unset($note, $link);

// Graph edges
$edges = [];
$edgeSet = [];
foreach ($notes as $slug => $note) {
    foreach ($note['links'] as $link) {
        if ($link['resolved']) {
            $pair = [$slug, $link['resolved']];
            sort($pair);
            $key = $pair[0] . '|' . $pair[1];
            if (!isset($edgeSet[$key])) {
                $edgeSet[$key] = true;
                $edges[] = ['source' => $pair[0], 'target' => $pair[1]];
            }
        }
    }
}

$nodes = [];
foreach ($notes as $slug => $note) {
    $degree = 0;
    foreach ($edges as $e) {
        if ($e['source'] === $slug || $e['target'] === $slug) {
            $degree++;
        }
    }
    $nodes[] = [
        'id'     => $slug,
        'title'  => $note['title'],
        'folder' => $note['folder'],
        'color'  => $note['color'],
        'degree' => $degree,
    ];
}

$output = [
    'generated'  => date('c'),
    'noteCount'  => count($notes),
    'edgeCount'  => count($edges),
    'tree'       => buildTree($vaultRoot),
    'notes'      => $notes,
    'graph'      => ['nodes' => $nodes, 'edges' => $edges],
    'unresolved' => $unresolved,
];

if (!is_dir(SHIFT_JSON)) {
    mkdir(SHIFT_JSON, 0755, true);
}

$outFile = SHIFT_JSON . '/vault.json';
file_put_contents($outFile, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Wrote {$output['noteCount']} notes, {$output['edgeCount']} edges to $outFile\n";
if (count($unresolved) > 0) {
    echo "WARN: " . count($unresolved) . " unresolved wiki-links\n";
}
