<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Ontology Overview';
$ontology = shift_ontology();
$stats = $ontology['stats'] ?? [];

include __DIR__ . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-1" style="color: var(--shift-navy)">SHIFT Ontology</h2>
        <p class="text-muted">Version <?= shift_e(SHIFT_VERSION) ?> — <?= shift_e(SHIFT_IRI_BASE) ?></p>
    </div>
</div>

<div class="card shift-version-card p-4 mb-4">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted w-25">Version</th><td><?= shift_e(SHIFT_VERSION) ?></td></tr>
                <tr><th class="text-muted">Version IRI</th><td><code class="shift-mono"><?= shift_e(SHIFT_IRI_BASE) ?></code></td></tr>
                <tr><th class="text-muted">Issued</th><td>2026</td></tr>
                <tr><th class="text-muted">Creator</th><td>Brian O'Regan, Energy Informatics Group</td></tr>
                <tr><th class="text-muted">Publisher</th><td>Tyndall National Institute / IERC, University College Cork</td></tr>
                <tr><th class="text-muted">Licence</th><td><a href="https://creativecommons.org/licenses/by/4.0/">CC BY 4.0</a></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Statistics</h6>
            <div class="row g-2">
                <div class="col-6"><div class="border rounded p-2 text-center"><div class="h4 mb-0"><?= $stats['classes'] ?? 0 ?></div><small class="text-muted">Classes</small></div></div>
                <div class="col-6"><div class="border rounded p-2 text-center"><div class="h4 mb-0"><?= ($stats['objectProperties'] ?? 0) + ($stats['datatypeProperties'] ?? 0) ?></div><small class="text-muted">Properties</small></div></div>
                <div class="col-6"><div class="border rounded p-2 text-center"><div class="h4 mb-0"><?= $stats['rules'] ?? 0 ?></div><small class="text-muted">Reasoning Rules</small></div></div>
                <div class="col-6"><div class="border rounded p-2 text-center"><div class="h4 mb-0"><?= shift_vault()['noteCount'] ?? 0 ?></div><small class="text-muted">KG Notes</small></div></div>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="data/json/ontology.json" class="badge bg-secondary text-decoration-none">JSON</a>
                <a href="downloads.php" class="badge text-decoration-none" style="background: var(--shift-blue); color:#fff">All Downloads</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="ontology-hierarchy.php" class="card shift-card p-3 text-decoration-none text-dark d-block">
            <h6><i data-lucide="git-branch" class="me-1"></i> Class Hierarchy</h6>
            <p class="small text-muted mb-0">Interactive subclass tree</p>
        </a>
    </div>
    <div class="col-md-4">
        <a href="ontology-reference.php" class="card shift-card p-3 text-decoration-none text-dark d-block">
            <h6><i data-lucide="list" class="me-1"></i> Reference Index</h6>
            <p class="small text-muted mb-0">WIDOCO-style all-terms page</p>
        </a>
    </div>
    <div class="col-md-4">
        <a href="rules.php" class="card shift-card p-3 text-decoration-none text-dark d-block">
            <h6><i data-lucide="brain" class="me-1"></i> Reasoning Rules</h6>
            <p class="small text-muted mb-0"><?= $stats['rules'] ?? 0 ?> SHIFT-RR rules</p>
        </a>
    </div>
</div>

<h4 class="mt-4 mb-3" style="color: var(--shift-navy)">Classes</h4>
<div class="table-responsive">
    <table class="table table-hover shift-table">
        <thead><tr><th>Name</th><th>Label</th><th>Parent</th><th>Source</th></tr></thead>
        <tbody>
        <?php foreach ($ontology['classes'] as $c): ?>
        <tr>
            <td><a href="ontology-class.php?name=<?= urlencode($c['name']) ?>"><code class="shift-mono"><?= shift_e($c['name']) ?></code></a></td>
            <td><?= shift_e($c['label']) ?></td>
            <td><?= shift_e(implode(', ', $c['subClassOf'] ?? [])) ?></td>
            <td class="small text-muted"><?= shift_e($c['source'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
