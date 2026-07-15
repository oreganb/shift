<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Reasoning Rules';
$ontology = shift_ontology();
$rules = $ontology['rules'] ?? [];

// 13 executable per SPECS — first 13 by ID for demo; real status would come from vault
$executableIds = array_slice(array_keys($rules), 0, 13);

include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-2" style="color: var(--shift-navy)">SHIFT Reasoning Rules</h2>
<p class="text-muted mb-4"><?= count($rules) ?> rules total — <?= count($executableIds) ?> with executable SPARQL, <?= count($rules) - count($executableIds) ?> specification-only.</p>

<div class="table-responsive">
    <table class="table table-hover shift-table">
        <thead>
            <tr><th>ID</th><th>Label</th><th>Status</th><th>Applies To</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rules as $id => $r):
            $exec = in_array($id, $executableIds, true);
        ?>
        <tr>
            <td><a href="rule.php?id=<?= urlencode($id) ?>"><code class="shift-mono"><?= shift_e($id) ?></code></a></td>
            <td><?= shift_e($r['label']) ?></td>
            <td>
                <?php if ($exec): ?>
                <span class="badge rule-badge-executable">SWRL / SPARQL</span>
                <?php else: ?>
                <span class="badge rule-badge-spec">Spec only</span>
                <?php endif; ?>
            </td>
            <td class="small"><?= shift_e($r['appliesToClass'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
