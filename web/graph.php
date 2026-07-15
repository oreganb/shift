<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Knowledge Graph';
$noteSlug = $_GET['note'] ?? '';
$focus = $_GET['focus'] ?? $_GET['search'] ?? '';

$extraScripts = ['js/kg-viewer.js'];
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="mb-0" style="color: var(--shift-navy)">Knowledge Graph</h2>
    <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-outline-secondary active" id="btnViewNote">Notes</button>
        <button type="button" class="btn btn-outline-secondary" id="btnViewGraph">Global Graph</button>
    </div>
</div>

<div class="kg-layout" id="kgLayout">
    <aside class="kg-pane kg-pane-sidebar p-2">
        <input type="search" class="form-control form-control-sm mb-2" id="kgTreeSearch" placeholder="Filter notes…">
        <div id="kgTree"></div>
    </aside>
    <main class="kg-pane kg-pane-main" id="kgMain">
        <div id="kgNoteView">
            <div class="kg-note-meta" id="kgNoteMeta"></div>
            <div class="p-3" id="kgNoteContent"><p class="text-muted">Select a note from the tree or search.</p></div>
        </div>
        <canvas id="kgGraphCanvas" class="kg-graph-canvas d-none"></canvas>
    </main>
    <aside class="kg-pane kg-pane-backlinks p-2">
        <h6 class="small text-uppercase text-muted">Backlinks</h6>
        <div id="kgBacklinks"></div>
        <hr>
        <h6 class="small text-uppercase text-muted">Local Graph</h6>
        <canvas id="kgLocalGraph" height="180" class="w-100"></canvas>
    </aside>
</div>

<script>
window.SHIFT_KG = {
    initialNote: <?= json_encode($noteSlug) ?>,
    initialFocus: <?= json_encode($focus) ?>
};
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
