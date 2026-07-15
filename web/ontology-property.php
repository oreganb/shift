<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$name = $_GET['name'] ?? '';
$ontology = shift_ontology();
$prop = $ontology['objectProperties'][$name] ?? $ontology['datatypeProperties'][$name] ?? null;
$isObject = isset($ontology['objectProperties'][$name]);

if (!$prop) {
    http_response_code(404);
    $pageTitle = 'Property Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-warning">Property not found.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $prop['name'];
include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="ontology.php">Ontology</a></li>
        <li class="breadcrumb-item active"><?= shift_e($prop['name']) ?></li>
    </ol>
</nav>

<h2 style="color: var(--shift-navy)"><?= shift_e($prop['label']) ?></h2>
<p class="shift-mono small"><?= shift_e($prop['iri']) ?></p>
<span class="badge mb-3" style="background: var(--shift-blue); color:#fff"><?= $isObject ? 'Object Property' : 'Datatype Property' ?></span>

<?php if ($prop['comment']): ?><p><?= shift_e($prop['comment']) ?></p><?php endif; ?>

<table class="table table-sm w-auto">
    <tr><th>Domain</th><td><?php if ($prop['domain']): ?><a href="ontology-class.php?name=<?= urlencode($prop['domain']) ?>"><?= shift_e($prop['domain']) ?></a><?php else: ?>—<?php endif; ?></td></tr>
    <tr><th>Range</th><td><?php if ($isObject && $prop['range']): ?><a href="ontology-class.php?name=<?= urlencode($prop['range']) ?>"><?= shift_e($prop['range']) ?></a><?php else: ?><code class="shift-mono"><?= shift_e($prop['range']) ?></code><?php endif; ?></td></tr>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
