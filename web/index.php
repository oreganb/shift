<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Home';
$pageDescription = 'Canonical documentation home for the SHIFT Ontology — Semantic Hierarchy for Intelligent Flexibility & Trading';

$ontology = shift_ontology();
$stats = $ontology['stats'] ?? [];

include __DIR__ . '/includes/header.php';
?>

<div class="shift-hero">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <img src="images/SHIFT_Logo.png" alt="SHIFT" height="64" class="mb-3">
            <h1>Semantic Hierarchy for Intelligent Flexibility &amp; Trading</h1>
            <p class="lead mb-4 opacity-90">A formal ontology for flexibility services, transactive energy, and grid-market interoperability — designed to complement SAREF, SEAS, CIM, OpenADR and SGAM.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="ontology.php" class="btn shift-btn-primary">Browse the Ontology</a>
                <a href="graph.php" class="btn shift-btn-outline">Open the Knowledge Graph</a>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block text-center">
            <div class="display-4 fw-bold text-warning"><?= (int)($stats['classes'] ?? 0) ?></div>
            <div class="small opacity-75">OWL Classes</div>
            <div class="row mt-3 text-center">
                <div class="col-4">
                    <div class="h4 mb-0"><?= (int)($stats['objectProperties'] ?? 0) ?></div>
                    <div class="small opacity-75">Object Props</div>
                </div>
                <div class="col-4">
                    <div class="h4 mb-0"><?= (int)($stats['datatypeProperties'] ?? 0) ?></div>
                    <div class="small opacity-75">Data Props</div>
                </div>
                <div class="col-4">
                    <div class="h4 mb-0"><?= (int)($stats['rules'] ?? 0) ?></div>
                    <div class="small opacity-75">Rules</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shift-card p-4">
            <div class="shift-card-icon navy mb-3"><i data-lucide="book-open"></i></div>
            <h5>Ontology Documentation</h5>
            <p class="text-muted small">Browse classes, properties, hierarchy and a WIDOCO-style reference index. Versioned IRIs and multiple serialisations.</p>
            <a href="ontology.php" class="text-decoration-none fw-semibold" style="color: var(--shift-blue)">Explore →</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shift-card p-4">
            <div class="shift-card-icon green mb-3"><i data-lucide="share-2"></i></div>
            <h5>Knowledge Graph Viewer</h5>
            <p class="text-muted small">Obsidian-style vault browser with wiki-links, backlinks, folder tree and interactive force-directed graph.</p>
            <a href="graph.php" class="text-decoration-none fw-semibold" style="color: var(--shift-blue)">Open Graph →</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shift-card p-4">
            <div class="shift-card-icon amber mb-3"><i data-lucide="link"></i></div>
            <h5>Standards Alignments</h5>
            <p class="text-muted small">SKOS mappings to SAREF, SEAS, CIM, OpenADR and SGAM — complement, not replace.</p>
            <a href="alignments.php" class="text-decoration-none fw-semibold" style="color: var(--shift-blue)">View Alignments →</a>
        </div>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3" style="color: var(--shift-navy)">SHIFT in the Standards Landscape</h5>
    <div class="d-flex flex-wrap gap-2">
        <?php
        $standards = ['SAREF' => 'saref', 'SEAS' => 'seas', 'CIM' => 'cim', 'OpenADR' => 'openadr', 'SGAM' => 'sgam'];
        foreach ($standards as $label => $slug): ?>
        <a href="alignment.php?target=<?= $slug ?>" class="badge text-decoration-none px-3 py-2" style="background: var(--shift-navy); color: #fff; font-size: 0.9rem;"><?= $label ?></a>
        <?php endforeach; ?>
        <span class="badge px-3 py-2" style="background: var(--shift-amber); color: var(--shift-navy-2); font-size: 0.9rem;">SHIFT</span>
    </div>
</div>

<div class="card p-4">
    <h5 id="citation" style="color: var(--shift-navy)">Citation</h5>
    <p class="text-muted small">If you use the SHIFT Ontology, please cite the project and this website.</p>
    <pre class="shift-mono p-3 rounded" style="background: rgba(31,61,122,0.06); font-size: 0.8rem; overflow-x: auto">@misc{shift-ontology,
  title  = {SHIFT: Semantic Hierarchy for Intelligent Flexibility and Trading},
  author = {O'Regan, Brian and Energy Informatics Lead},
  year   = {2026},
  url    = {https://shift-ontology.org}
}</pre>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
