<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Ontology Reference';
$ontology = shift_ontology();

include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-2" style="color: var(--shift-navy)">Ontology Reference</h2>
<p class="text-muted mb-4">Single-page index of all terms — WIDOCO-style.</p>

<div class="row">
    <div class="col-lg-3">
        <nav class="sticky-top pt-2" style="top: 80px">
            <h6 class="text-uppercase small text-muted">Contents</h6>
            <ul class="list-unstyled small">
                <li><a href="#classes">Classes</a></li>
                <li><a href="#object-properties">Object Properties</a></li>
                <li><a href="#datatype-properties">Datatype Properties</a></li>
                <li><a href="#rules">Rules</a></li>
            </ul>
        </nav>
    </div>
    <div class="col-lg-9">
        <h4 id="classes" style="color: var(--shift-navy)">Classes <a href="#classes" class="small">↑</a></h4>
        <?php foreach ($ontology['classes'] as $c): ?>
        <div class="mb-3 pb-2 border-bottom" id="class-<?= shift_e($c['name']) ?>">
            <h5><a href="ontology-class.php?name=<?= urlencode($c['name']) ?>"><code class="shift-mono"><?= shift_e($c['name']) ?></code></a></h5>
            <p class="small text-muted mb-0"><?= shift_e($c['comment'] ?: 'No definition in source module.') ?></p>
        </div>
        <?php endforeach; ?>

        <h4 id="object-properties" class="mt-4" style="color: var(--shift-navy)">Object Properties</h4>
        <?php foreach ($ontology['objectProperties'] as $p): ?>
        <div class="mb-2" id="prop-<?= shift_e($p['name']) ?>">
            <a href="ontology-property.php?name=<?= urlencode($p['name']) ?>"><code class="shift-mono"><?= shift_e($p['name']) ?></code></a>
            <span class="text-muted small"> — domain: <?= shift_e($p['domain']) ?>, range: <?= shift_e($p['range']) ?></span>
        </div>
        <?php endforeach; ?>

        <h4 id="datatype-properties" class="mt-4" style="color: var(--shift-navy)">Datatype Properties</h4>
        <?php foreach ($ontology['datatypeProperties'] as $p): ?>
        <div class="mb-2">
            <a href="ontology-property.php?name=<?= urlencode($p['name']) ?>"><code class="shift-mono"><?= shift_e($p['name']) ?></code></a>
            <span class="text-muted small"> — domain: <?= shift_e($p['domain']) ?>, range: <?= shift_e($p['range']) ?></span>
        </div>
        <?php endforeach; ?>

        <h4 id="rules" class="mt-4" style="color: var(--shift-navy)">Rules</h4>
        <?php foreach ($ontology['rules'] as $r): ?>
        <div class="mb-2">
            <a href="rule.php?id=<?= urlencode($r['id']) ?>"><?= shift_e($r['label']) ?></a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
