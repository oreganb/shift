<?php
if (isset($title)) {
    echo "<div class='row justify-content-center py-5'>
    <div class='col-xxl-5 col-xl-7 text-center'>
        <span class='badge badge-default fw-normal shadow px-2 py-1 mb-2 fst-italic fs-xxs'>
            <i data-lucide='$badgeIcon' class='fs-sm me-1'></i> $badgeTitle
        </span>
        <h3 class='fw-bold'>
            $title
        </h3>

        <p class='fs-md text-muted mb-0'>
            $subTitle
        </p>
    </div>
</div>";
}


?>