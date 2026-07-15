        </div><!-- /.container-fluid -->
        <footer class="shift-footer py-4 mt-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="images/SHIFT_Logo.png" alt="SHIFT" height="32" class="me-2">
                        <span class="text-muted small">SHIFT Ontology v<?= SHIFT_VERSION ?> — Tyndall / IERC, UCC</span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <?php
                        $fresh = shift_load_json('ontology.json');
                        $gen = $fresh['generated'] ?? date('c');
                        ?>
                        <span class="text-muted small">Built <?= shift_e(substr($gen, 0, 10)) ?></span>
                        · <a href="about.php#citation">Cite</a>
                        · <a href="downloads.php">Downloads</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Search modal -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <input type="search" id="globalSearchInput" class="form-control form-control-lg border-0 shadow-none" placeholder="Search classes, properties, rules, notes… (e.g. Flexumer)" autofocus>
                <kbd class="text-muted ms-2">Esc</kbd>
            </div>
            <div class="modal-body pt-2" id="searchResults"></div>
        </div>
    </div>
</div>

<script src="assets/js/vendors.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="js/shift.js"></script>
<?php if (!empty($extraScripts)): foreach ((array)$extraScripts as $s): ?>
<script src="<?= shift_e($s) ?>"></script>
<?php endforeach; endif; ?>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
