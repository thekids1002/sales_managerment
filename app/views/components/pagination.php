<?php
$baseUrl = isset($baseUrl) ? $baseUrl : ''; // URL cơ bản
$queryParams = isset($queryParams) ? $queryParams : []; // Các tham số truy vấn khác

// Tạo chuỗi truy vấn từ các tham số
$queryString = http_build_query(array_merge($queryParams, ['per_page' => $perPage]));
?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($pagination['current_page'] > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= baseUrl("{$baseUrl}?page=1&{$queryString}") ?>">
                    First
                </a>
            </li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= baseUrl("{$baseUrl}?page={$i}&{$queryString}") ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <li class="page-item">
                <a class="page-link" href="<?= baseUrl("{$baseUrl}?page={$pagination['last_page']}&{$queryString}") ?>">
                    Last
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>