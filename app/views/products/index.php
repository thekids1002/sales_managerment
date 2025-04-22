<?php ob_start(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
        <a href="<?= baseUrl('/products/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
    <div class="card-body">
        <!-- Category Filter -->
        <div class="mb-4">
            <h6 class="mb-2">Filter by Category:</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= baseUrl('/products') ?>" class="btn <?= !isset($currentCategory) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    All Categories
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="<?= baseUrl("/products/category/{$category['id']}") ?>"
                        class="btn <?= isset($currentCategory) && $currentCategory == $category['id'] ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                        <?= e($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="productsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= e($product['id']) ?></td>
                                <td><?= e($product['name']) ?></td>
                                <td>
                                    <?php if (isset($product['category_name']) && $product['category_name']): ?>
                                        <span class="badge bg-info text-dark"><?= e($product['category_name']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatPrice($product['price']) ?></td>
                                <td>
                                    <?php if ($product['quantity'] < 10): ?>
                                        <span class="text-danger"><?= e($product['quantity']) ?></span>
                                    <?php else: ?>
                                        <?= e($product['quantity']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= baseUrl("/products/{$product['id']}/edit") ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <?php if (isset($pagination) && count($products) > 0): ?>
                <?php 
                $baseUrl = '/products';
                include VIEWS_PATH . '/components/pagination.php'; 
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('productsTable');
        if (table) {
            new DataTable(table, {
                order: [
                    [0, 'desc']
                ]
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>