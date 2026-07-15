<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'About';

include __DIR__ . '/includes/header.php';
?>

<h2 style="color: var(--shift-navy)">About SHIFT</h2>

<p>The <strong>SHIFT Ontology</strong> (Semantic Hierarchy for Intelligent Flexibility &amp; Trading) is a formal knowledge representation for flexibility services, transactive energy workflows, and grid–market interoperability.</p>

<p><strong>Owner:</strong> Brian O'Regan, Energy Informatics Group, Tyndall National Institute / IERC, University College Cork.</p>

<p><strong>Funding:</strong> CET Partnership Cetp-FP2023-00114, EC GA 101069750, SEAI.</p>

<p><strong>Licence:</strong> <a href="https://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 (CC BY 4.0)</a></p>

<h4 id="citation" style="color: var(--shift-navy)">Citation</h4>
<pre class="shift-mono p-3 rounded" style="background: rgba(31,61,122,0.06); font-size: 0.85rem">@misc{shift-ontology-2026,
  title        = {SHIFT: Semantic Hierarchy for Intelligent Flexibility and Trading},
  author       = {O'Regan, Brian and Energy Informatics Group},
  institution  = {Tyndall National Institute, IERC, University College Cork},
  year         = {2026},
  howpublished = {\url{https://shift-ontology.org}},
  note         = {Ontology version <?= SHIFT_VERSION ?>}
}</pre>

<h4 style="color: var(--shift-navy)">Concepts</h4>
<ul>
    <li><a href="alignment.php?target=sgam">SGAM Placement</a> — architecture layer mapping</li>
    <li><a href="graph.php?search=FLEXUS">FLEXUS Platform</a> — research platform context</li>
</ul>

<h4 style="color: var(--shift-navy)">Content Integrity</h4>
<ul class="small text-muted">
    <li>SHIFT is <em>designed into</em> research platforms; deployment evidence is pending.</li>
    <li>External ontologies (SAREF, TNO CIM) are linked, not rehosted.</li>
    <li>Alignments use SKOS only — no owl:equivalentClass in published mappings.</li>
</ul>

<?php include __DIR__ . '/includes/footer.php'; ?>
