<?php ob_start(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Order List</h6>
        <a href="<?= baseUrl('/orders/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create New Order
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="ordersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order Date</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><a href="<?= baseUrl("/orders/{$order['id']}") ?>"><?= e($order['id']) ?></a></td>
                                <td><?= formatDate($order['created_at'], 'Y-m-d H:i') ?></td>
                                <td><?= e($order['customer_name']) ?></td>
                                <td><?= formatPrice($order['total_amount']) ?></td>
                                <td><?= e(isset($order['notes']) ? $order['notes'] : '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if (isset($pagination)): ?>
                <?php 
                $perPage = $pagination['per_page'];
                $baseUrl = '/orders';
                include VIEWS_PATH . '/components/pagination.php'; 
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>