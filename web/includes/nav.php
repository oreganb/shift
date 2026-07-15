<?php
$current = shift_current_page();
$navItems = [
    ['page' => 'index',       'href' => 'index.php',              'label' => 'Home',      'icon' => 'home'],
    ['page' => 'ontology',    'href' => 'ontology.php',           'label' => 'Ontology',  'icon' => 'book-open'],
    ['page' => 'ontology-hierarchy', 'href' => 'ontology-hierarchy.php', 'label' => 'Hierarchy', 'icon' => 'git-branch'],
    ['page' => 'ontology-reference', 'href' => 'ontology-reference.php', 'label' => 'Reference', 'icon' => 'list'],
    ['page' => 'rules',       'href' => 'rules.php',              'label' => 'Rules',     'icon' => 'brain'],
    ['page' => 'graph',       'href' => 'graph.php',              'label' => 'Knowledge Graph', 'icon' => 'share-2'],
    ['page' => 'alignments',  'href' => 'alignments.php',         'label' => 'Alignments','icon' => 'link'],
    ['page' => 'downloads',   'href' => 'downloads.php',          'label' => 'Downloads', 'icon' => 'download'],
    ['page' => 'about',       'href' => 'about.php',              'label' => 'About',     'icon' => 'info'],
];
?>
<header class="app-topbar shift-topbar">
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-3 w-100">
            <div class="logo-topbar">
                <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="images/SHIFT_Logo.png" alt="SHIFT Ontology" height="36">
                </a>
            </div>
            <button class="button-collapse-toggle d-xl-none btn btn-link text-white">
                <i data-lucide="menu"></i>
            </button>
            <nav class="d-none d-xl-flex gap-1 flex-grow-1">
                <?php foreach ($navItems as $item): ?>
                <a href="<?= $item['href'] ?>" class="shift-nav-link <?= $current === $item['page'] ? 'active' : '' ?>">
                    <?= shift_e($item['label']) ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <div class="ms-auto d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm shift-btn-search" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search (Ctrl+K)">
                    <i data-lucide="search" class="me-1"></i> Search
                </button>
                <span class="badge shift-badge-version">v<?= SHIFT_VERSION ?></span>
            </div>
        </div>
    </div>
</header>

<div class="sidenav-menu shift-sidenav d-xl-none">
    <div class="scrollbar" data-simplebar>
        <ul class="side-nav">
            <?php foreach ($navItems as $item): ?>
            <li class="side-nav-item">
                <a href="<?= $item['href'] ?>" class="side-nav-link <?= $current === $item['page'] ? 'active' : '' ?>">
                    <span class="menu-icon"><i data-lucide="<?= $item['icon'] ?>"></i></span>
                    <span class="menu-text"><?= shift_e($item['label']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
