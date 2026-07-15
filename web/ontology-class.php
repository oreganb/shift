<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$name = $_GET['name'] ?? '';
$ontology = shift_ontology();
$cls = $ontology['classes'][$name] ?? null;

if (!$cls) {
    http_response_code(404);
    $pageTitle = 'Class Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-warning">Class not found: ' . shift_e($name) . '</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $cls['name'];
$objProps = array_filter($ontology['objectProperties'], function ($p) use ($name) {
    return ($p['domain'] ?? '') === $name;
});
$dtProps = array_filter($ontology['datatypeProperties'], function ($p) use ($name) {
    return ($p['domain'] ?? '') === $name;
});
$relatedRules = array_filter($ontology['rules'], function ($r) use ($name) {
    return str_contains($r['comment'] ?? '', $name)
        || ($r['appliesToClass'] ?? '') === SHIFT_IRI_BASE . $name
        || ($r['infersClass'] ?? '') === SHIFT_IRI_BASE . $name;
});

include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="ontology.php">Ontology</a></li>
        <li class="breadcrumb-item active"><?= shift_e($cls['name']) ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h2 style="color: var(--shift-navy)"><?= shift_e($cls['label']) ?></h2>
        <p class="shift-mono small text-muted mb-1"><?= shift_e($cls['iri']) ?>
            <button class="btn btn-sm btn-link p-0 ms-1" onclick="navigator.clipboard.writeText('<?= shift_e($cls['iri']) ?>')" title="Copy IRI"><i data-lucide="copy"></i></button>
        </p>
    </div>
    <a href="graph.php?search=<?= urlencode($cls['name']) ?>" class="btn btn-sm shift-btn-primary">View in Graph</a>
</div>

<?php if ($cls['comment']): ?>
<p><?= shift_e($cls['comment']) ?></p>
<?php endif; ?>

<div class="card p-3 mb-4"><?= shift_class_diagram($cls, $ontology) ?></div>

<?php if (!empty($cls['subClassOf'])): ?>
<p><strong>Subclass of:</strong>
    <?php foreach ($cls['subClassOf'] as $p): ?>
    <a href="ontology-class.php?name=<?= urlencode($p) ?>"><code class="shift-mono"><?= shift_e($p) ?></code></a>
    <?php endforeach; ?>
</p>
<?php endif; ?>

<?php $children = $ontology['subclasses'][$name] ?? []; if ($children): ?>
<p><strong>Direct subclasses:</strong>
    <?php foreach ($children as $c): ?>
    <a href="ontology-class.php?name=<?= urlencode($c) ?>"><code class="shift-mono"><?= shift_e($c) ?></code></a>
    <?php endforeach; ?>
</p>
<?php endif; ?>

<h5 style="color: var(--shift-navy)">Properties</h5>
<div class="table-responsive">
    <table class="table table-sm shift-table">
        <thead><tr><th>Property</th><th>Type</th><th>Range</th></tr></thead>
        <tbody>
        <?php foreach ($objProps as $p): ?>
        <tr>
            <td><a href="ontology-property.php?name=<?= urlencode($p['name']) ?>"><code class="shift-mono"><?= shift_e($p['name']) ?></code></a></td>
            <td>Object</td>
            <td><a href="ontology-class.php?name=<?= urlencode($p['range']) ?>"><?= shift_e($p['range']) ?></a></td>
        </tr>
        <?php endforeach; ?>
        <?php foreach ($dtProps as $p): ?>
        <tr>
            <td><a href="ontology-property.php?name=<?= urlencode($p['name']) ?>"><code class="shift-mono"><?= shift_e($p['name']) ?></code></a></td>
            <td>Datatype</td>
            <td><code class="shift-mono"><?= shift_e($p['range']) ?></code></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($objProps) && empty($dtProps)): ?>
        <tr><td colspan="3" class="text-muted">No direct properties defined in legacy modules.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($relatedRules): ?>
<h5 style="color: var(--shift-navy)">Related Rules</h5>
<ul>
<?php foreach ($relatedRules as $r): ?>
<li><a href="rule.php?id=<?= urlencode($r['id']) ?>"><?= shift_e($r['label']) ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
