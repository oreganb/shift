<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Class Hierarchy';
$ontology = shift_ontology();

include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-3" style="color: var(--shift-navy)">Class Hierarchy</h2>
<p class="text-muted">Subclass relationships from the SHIFT legacy OWL modules.</p>

<div class="card p-4">
    <?= shift_hierarchy_tree($ontology) ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
