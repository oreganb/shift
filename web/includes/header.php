<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

/** @var string $pageTitle */
/** @var string|null $pageDescription */
$pageTitle = $pageTitle ?? 'SHIFT Ontology';
$pageDescription = $pageDescription ?? 'Semantic Hierarchy for Intelligent Flexibility & Trading';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= shift_e($pageTitle) ?> — SHIFT Ontology</title>
    <meta name="description" content="<?= shift_e($pageDescription) ?>">
    <link rel="icon" href="images/SHIFT_Logo.png" type="image/png">
    <script src="assets/js/config.js"></script>
    <link href="assets/css/vendors.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
    <link href="css/shift.css" rel="stylesheet">
    <script src="assets/plugins/lucide/lucide.min.js"></script>
</head>
<body class="shift-body">
<div class="wrapper">
    <?php include __DIR__ . '/nav.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
