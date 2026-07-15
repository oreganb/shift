<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$id = $_GET['id'] ?? '';
$ontology = shift_ontology();
$rule = $ontology['rules'][$id] ?? null;

if (!$rule) {
    http_response_code(404);
    $pageTitle = 'Rule Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-warning">Rule not found.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $rule['label'];
$executableIds = array_slice(array_keys($ontology['rules']), 0, 13);
$isExecutable = in_array($id, $executableIds, true);

include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="rules.php">Rules</a></li>
        <li class="breadcrumb-item active"><?= shift_e($id) ?></li>
    </ol>
</nav>

<h2 style="color: var(--shift-navy)"><?= shift_e($rule['label']) ?></h2>
<p class="shift-mono small"><?= shift_e($rule['iri']) ?></p>

<?php if ($isExecutable): ?>
<span class="badge rule-badge-executable mb-3">Executable (SWRL / SPARQL)</span>
<?php else: ?>
<span class="badge rule-badge-spec mb-3">Specification only — not yet executable</span>
<?php endif; ?>

<div class="card p-4 mb-4">
    <h5>Description</h5>
    <p><?= shift_e($rule['comment'] ?? '') ?></p>

    <?php if (!empty($rule['condition1']) || !empty($rule['condition2'])): ?>
    <h6>Conditions</h6>
    <ul class="shift-mono small">
        <?php if (!empty($rule['condition1'])): ?><li><?= shift_e($rule['condition1']) ?></li><?php endif; ?>
        <?php if (!empty($rule['condition2'])): ?><li><?= shift_e($rule['condition2']) ?></li><?php endif; ?>
    </ul>
    <?php endif; ?>

    <?php if (!empty($rule['result'])): ?>
    <h6>Result</h6>
    <pre class="shift-mono p-3 rounded" style="background: rgba(31,61,122,0.06)"><?= shift_e($rule['result']) ?></pre>
    <?php endif; ?>
</div>

<?php if ($isExecutable): ?>
<div class="card p-4">
    <h5>SPARQL (placeholder)</h5>
    <pre class="shift-mono p-3 rounded" style="background: rgba(31,61,122,0.06); font-size: 0.8rem"># Executable SPARQL for <?= shift_e($id) ?> ships in the rules download bundle.
PREFIX shift: &lt;http://shift-ontology.org/core#&gt;
SELECT ?x WHERE {
  ?x a shift:Actor .
}</pre>
</div>
<?php endif; ?>

<p class="mt-3"><a href="graph.php?search=<?= urlencode($id) ?>">View related notes in Knowledge Graph →</a></p>

<?php include __DIR__ . '/includes/footer.php'; ?>
