<?php ob_start(); ?>

<div class="mb-3">
    <a href="<?= baseUrl('/orders') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
    <a href="<?= baseUrl("/orders/{$order['id']}/print") ?>" target="_blank" class="btn btn-info">
        <i class="fas fa-print"></i> Print Order
    </a>
</div>

<div class="row">
    <!-- Order Details -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Order #<?= e($order['id']) ?></h6>
                <span class="text-muted"><?= formatDate($order['order_date'], 'Y-m-d H:i') ?></span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong></p>
                        <p><?= e($order['customer_name']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Contact:</strong></p>
                        <p>
                            <?php if (!empty($order['customer_email'])): ?>
                                <?= e($order['customer_email']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($order['customer_phone'])): ?>
                                <?= e($order['customer_phone']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <?php if (!empty($order['customer_address'])): ?>
                <div class="mb-3">
                    <p class="mb-1"><strong>Shipping Address:</strong></p>
                    <p><?= e($order['customer_address']) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($order['notes'])): ?>
                <div class="mb-3">
                    <p class="mb-1"><strong>Notes:</strong></p>
                    <p><?= e($order['notes']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= e($item['product_name']) ?></td>
                                    <td><?= formatPrice($item['unit_price']) ?></td>
                                    <td><?= e($item['quantity']) ?></td>
                                    <td><?= formatPrice($item['unit_price'] * $item['quantity']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th><?= formatPrice($order['total_amount']) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>