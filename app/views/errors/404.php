<?php ob_start(); ?>

<div class="text-center">
    <div class="error mx-auto" data-text="404">
        <h1 class="display-1 text-danger">404</h1>
    </div>
    <p class="lead text-gray-800 mb-5">Page Not Found</p>
    <p class="text-gray-500 mb-0">It seems you're trying to access a page that doesn't exist...</p>
    <a href="<?= baseUrl('/') ?>">&larr; Back to Home</a>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?> 