<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$target = $_GET['target'] ?? '';
$content = [
    'saref' => [
        'title' => 'SAREF Alignment',
        'warning' => null,
        'body' => 'SAREF models device-level services and functions. SHIFT models grid- and market-facing flexibility services. The saref:Service vs shift:FlexibilityService pair is a documented false friend.',
        'falseFriend' => ['saref:Service', 'shift:FlexibilityService', 'saref:Service is a device function exposed to a network; shift:FlexibilityService is a grid/market service delivered by modulating assets.'],
    ],
    'seas' => ['title' => 'SEAS Alignment', 'warning' => null, 'body' => 'SEAS provides market-oriented forecasting and control activity semantics. SHIFT links flexibility trades and contracts to SEAS activities via skos:relatedMatch.'],
    'cim' => ['title' => 'CIM Alignment', 'warning' => 'IRIs in CIM mapping notes are unverified against the TNO release until manually checked.', 'body' => 'CIM covers power system resources. SHIFT maps Actor, Node and MarketParticipant concepts with skos:closeMatch where appropriate.'],
    'openadr' => ['title' => 'OpenADR Alignment', 'warning' => null, 'body' => 'OpenADR formalisation (w3id.org/def/openadr, CC BY 4.0) provides demand-response event semantics. SHIFT maps FlexibilityService activation to OADR events via skos:relatedMatch.'],
    'sgam' => ['title' => 'SGAM Placement', 'warning' => null, 'body' => 'SGAM placement matrix for SHIFT concepts across Business, Function, Information, Communication and Component layers — not a term mapping table.'],
];

if (!isset($content[$target])) {
    http_response_code(404);
    header('Location: alignments.php');
    exit;
}

$c = $content[$target];
$pageTitle = $c['title'];

include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="alignments.php">Alignments</a></li>
        <li class="breadcrumb-item active"><?= shift_e($c['title']) ?></li>
    </ol>
</nav>

<h2 style="color: var(--shift-navy)"><?= shift_e($c['title']) ?></h2>

<?php if ($c['warning']): ?>
<div class="alert alert-warning"><?= shift_e($c['warning']) ?></div>
<?php endif; ?>

<p><?= shift_e($c['body']) ?></p>

<?php if (!empty($c['falseFriend'])): ?>
<div class="card border-warning p-4 mb-4">
    <h5 class="text-warning">False Friend Warning</h5>
    <p><code class="shift-mono"><?= shift_e($c['falseFriend'][0]) ?></code> vs <code class="shift-mono"><?= shift_e($c['falseFriend'][1]) ?></code></p>
    <p class="mb-0 small"><?= shift_e($c['falseFriend'][2]) ?></p>
</div>
<?php endif; ?>

<p class="text-muted small">External ontology files are not rehosted. See the Knowledge Graph vault under <code>08_Standards</code> for fetch instructions and mapping tables.</p>
<p><a href="graph.php?search=<?= urlencode($c['title']) ?>">Browse related vault notes →</a></p>

<?php include __DIR__ . '/includes/footer.php'; ?>
