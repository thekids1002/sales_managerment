<?php
$baseUrl = isset($baseUrl) ? $baseUrl : ''; // URL cơ bản
$queryParams = isset($queryParams) ? $queryParams : []; // Các tham số truy vấn khác

// Tạo chuỗi truy vấn từ các tham số
$queryString = http_build_query(array_merge($queryParams, ['per_page' => $pagination['per_page']]));
$queryString = !empty($queryString) ? "&{$queryString}" : "";

// Tính toán các trang hiển thị xung quanh trang hiện tại
$startPage = max(1, $pagination['current_page'] - 2);
$endPage = min($pagination['last_page'], $pagination['current_page'] + 2);
?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-4">
        <!-- First Page -->
        <li class="page-item <?= ($pagination['current_page'] <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= baseUrl("{$baseUrl}?page=1{$queryString}") ?>" aria-label="First">
                <span aria-hidden="true">Frist</span>
            </a>
        </li>
        
        <!-- Previous Page -->
        <li class="page-item <?= ($pagination['current_page'] <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= baseUrl("{$baseUrl}?page=" . ($pagination['current_page'] - 1) . "{$queryString}") ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        
        <!-- Page Numbers -->
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?= ($i == $pagination['current_page']) ? 'active' : '' ?>">
                <a class="page-link" href="<?= baseUrl("{$baseUrl}?page={$i}{$queryString}") ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        
        <!-- Next Page -->
        <li class="page-item <?= ($pagination['current_page'] >= $pagination['last_page']) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= baseUrl("{$baseUrl}?page=" . ($pagination['current_page'] + 1) . "{$queryString}") ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        
        <!-- Last Page -->
        <li class="page-item <?= ($pagination['current_page'] >= $pagination['last_page']) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= baseUrl("{$baseUrl}?page={$pagination['last_page']}{$queryString}") ?>" aria-label="Last">
                <span aria-hidden="true">Last</span>
            </a>
        </li>
    </ul>
</nav>