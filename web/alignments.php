<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Standards Alignments';

$alignments = [
    'saref'   => ['name' => 'SAREF', 'desc' => 'ETSI Smart Applications REFerence ontology for IoT devices and services.'],
    'seas'    => ['name' => 'SEAS', 'desc' => 'Smart Energy Aware Systems — energy market and forecasting semantics.'],
    'cim'     => ['name' => 'CIM', 'desc' => 'Common Information Model for power system resources (TNO release).'],
    'openadr' => ['name' => 'OpenADR', 'desc' => 'Open Automated Demand Response — event and resource semantics.'],
    'sgam'    => ['name' => 'SGAM', 'desc' => 'Smart Grid Architecture Model — layer and zone placement.'],
];

include __DIR__ . '/includes/header.php';
?>

<h2 class="mb-2" style="color: var(--shift-navy)">Standards Alignments</h2>
<p class="lead text-muted mb-4">SHIFT complements — does not replace — existing energy and IoT ontologies. All mappings use SKOS relations only.</p>

<div class="alert alert-info">
    <strong>Complement, not replace.</strong> SHIFT adds flexibility-service, transactive-energy and reasoning vocabulary where general-purpose standards stop.
</div>

<div class="row g-4">
<?php foreach ($alignments as $slug => $a): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card shift-card p-4">
            <h5><?= shift_e($a['name']) ?></h5>
            <p class="small text-muted"><?= shift_e($a['desc']) ?></p>
            <a href="alignment.php?target=<?= $slug ?>" class="fw-semibold" style="color: var(--shift-blue)">View alignment →</a>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
