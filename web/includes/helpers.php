<?php
require_once __DIR__ . '/config.php';

// PHP 7.x polyfills (shared hosting may not run PHP 8+)
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

function shift_load_json(string $file): array
{
    static $cache = [];
    if (!isset($cache[$file])) {
        $path = SHIFT_JSON . '/' . $file;
        if (!file_exists($path)) {
            return [];
        }
        $cache[$file] = json_decode(file_get_contents($path), true) ?: [];
    }
    return $cache[$file];
}

function shift_ontology(): array
{
    return shift_load_json('ontology.json');
}

function shift_vault(): array
{
    return shift_load_json('vault.json');
}

function shift_search_index(): array
{
    return shift_load_json('search-index.json');
}

function shift_e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function shift_current_page(): string
{
    return basename($_SERVER['PHP_SELF'] ?? 'index.php', '.php');
}

function shift_is_active(string $page): string
{
    return shift_current_page() === $page ? 'active' : '';
}

function shift_render_markdown(string $md): string
{
    $html = shift_e($md);
    // Headers
    $html = preg_replace('/^### (.+)$/m', '<h5>$1</h5>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h4>$1</h4>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h3>$1</h3>', $html);
    // Bold / code
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/`([^`]+)`/', '<code class="shift-mono">$1</code>', $html);
    // Wiki links
    $html = preg_replace_callback(
        '/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/',
        function ($m) {
            $target = $m[1];
            $label = $m[2] ?? $target;
            $vault = shift_vault();
            $slug = null;
            foreach ($vault['notes'] ?? [] as $s => $n) {
                if ($n['title'] === $target || in_array($target, $n['meta']['aliases'] ?? [], true)) {
                    $slug = $s;
                    break;
                }
            }
            if ($slug) {
                return '<a href="graph.php?note=' . urlencode($slug) . '" class="wiki-link">' . shift_e($label) . '</a>';
            }
            return '<span class="wiki-link-unresolved">' . shift_e($label) . '</span>';
        },
        $html
    );
    // Tables (simple)
    $html = preg_replace_callback('/(\|.+\|\n)+/', function ($m) {
        $rows = array_filter(explode("\n", trim($m[0])));
        if (count($rows) < 2) {
            return $m[0];
        }
        $out = '<div class="table-responsive"><table class="table table-sm table-bordered shift-table">';
        foreach ($rows as $i => $row) {
            if (preg_match('/^\|[\s\-:|]+\|$/', $row)) {
                continue;
            }
            $cells = array_map('trim', explode('|', trim($row, '|')));
            $tag = ($i === 0) ? 'th' : 'td';
            $out .= '<tr>';
            foreach ($cells as $cell) {
                $out .= "<$tag>" . preg_replace('/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/', '<a href="graph.php?note=$1">$2</a>', shift_e($cell)) . "</$tag>";
            }
            $out .= '</tr>';
        }
        return $out . '</table></div>';
    }, $html);
    // Paragraphs
    $html = preg_replace('/\n\n+/', '</p><p>', $html);
    return '<div class="shift-markdown"><p>' . $html . '</p></div>';
}

function shift_class_diagram(array $cls, array $ontology): string
{
    $name = $cls['name'];
    $parents = $cls['subClassOf'] ?? [];
    $children = $ontology['subclasses'][$name] ?? [];
    $svg = '<svg viewBox="0 0 400 120" class="shift-class-diagram w-100" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect x="150" y="40" width="100" height="36" rx="4" fill="#1F3D7A" stroke="#162C5C"/>';
    $svg .= '<text x="200" y="62" text-anchor="middle" fill="#FAFAF7" font-size="11">' . shift_e($name) . '</text>';
    foreach ($parents as $i => $p) {
        $x = 50 + $i * 80;
        $svg .= '<rect x="' . $x . '" y="0" width="80" height="28" rx="3" fill="#2E62B0" opacity="0.8"/>';
        $svg .= '<text x="' . ($x + 40) . '" y="18" text-anchor="middle" fill="#fff" font-size="9">' . shift_e($p) . '</text>';
        $svg .= '<line x1="' . ($x + 40) . '" y1="28" x2="200" y2="40" stroke="#2E62B0" stroke-width="1"/>';
    }
    foreach (array_slice($children, 0, 3) as $i => $c) {
        $x = 100 + $i * 70;
        $svg .= '<rect x="' . $x . '" y="88" width="70" height="28" rx="3" fill="#4A9440" opacity="0.8"/>';
        $svg .= '<text x="' . ($x + 35) . '" y="106" text-anchor="middle" fill="#fff" font-size="8">' . shift_e($c) . '</text>';
        $svg .= '<line x1="200" y1="76" x2="' . ($x + 35) . '" y2="88" stroke="#4A9440" stroke-width="1"/>';
    }
    $svg .= '</svg>';
    return $svg;
}

function shift_hierarchy_tree(array $ontology, ?string $parent = null, int $depth = 0): string
{
    $html = '<ul class="shift-tree list-unstyled ms-' . min($depth * 2, 4) . '">';
    foreach ($ontology['classes'] as $name => $cls) {
        $parents = $cls['subClassOf'] ?? [];
        $isRoot = empty($parents) || (count($parents) === 1 && $parents[0] === 'Thing');
        if ($parent === null && !$isRoot) {
            continue;
        }
        if ($parent !== null && !in_array($parent, $parents, true)) {
            continue;
        }
        $html .= '<li><a href="ontology-class.php?name=' . urlencode($name) . '">' . shift_e($name) . '</a>';
        $html .= shift_hierarchy_tree($ontology, $name, $depth + 1);
        $html .= '</li>';
    }
    return $html . '</ul>';
}
