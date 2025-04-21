<?php ob_start(); ?>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?> 