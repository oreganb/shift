<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Downloads';

include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-3" style="color: var(--shift-navy)">Downloads</h2>

<div class="table-responsive">
    <table class="table shift-table">
        <thead><tr><th>Resource</th><th>Version</th><th>Format</th><th>Link</th></tr></thead>
        <tbody>
            <tr>
                <td>SHIFT Ontology (JSON, generated)</td>
                <td><?= SHIFT_VERSION ?></td>
                <td>JSON</td>
                <td><a href="data/json/ontology.json">ontology.json</a></td>
            </tr>
            <tr>
                <td>Knowledge Graph vault (JSON, generated)</td>
                <td><?= SHIFT_VERSION ?></td>
                <td>JSON</td>
                <td><a href="data/json/vault.json">vault.json</a></td>
            </tr>
            <tr>
                <td>Search index</td>
                <td><?= SHIFT_VERSION ?></td>
                <td>JSON</td>
                <td><a href="data/json/search-index.json">search-index.json</a></td>
            </tr>
            <tr>
                <td>Legacy per-class OWL modules</td>
                <td>legacy</td>
                <td>RDF/XML</td>
                <td><a href="data/ontology/">data/ontology/</a></td>
            </tr>
            <tr>
                <td>Reasoning Rules (OWL)</td>
                <td>legacy</td>
                <td>RDF/XML</td>
                <td>35 files in <a href="data/ontology/">data/ontology/</a></td>
            </tr>
            <tr>
                <td>Obsidian vault (source)</td>
                <td>2026-07</td>
                <td>ZIP</td>
                <td><a href="downloads/SHIFT_Ontology_KG.zip">SHIFT_Ontology_KG.zip</a></td>
            </tr>
        </tbody>
    </table>
</div>

<p class="text-muted small">TTL, RDF/XML, JSON-LD and N-Triples serialisations for v0.1.1 will be generated from the normalised TBox at release. Legacy OWL modules lack ontology headers and are provided for reference only.</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
