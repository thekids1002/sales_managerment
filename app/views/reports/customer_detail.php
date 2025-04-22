<?php ob_start(); ?>

<div class="mb-3">
    <a href="<?= baseUrl('/reports/customer') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Purchase History: <?= e($customer['name']) ?></h6>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-info d-print-none">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Date Range Form -->
        <form method="get" class="mb-4 d-print-none">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Customer Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Customer Details</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong> <?= e($customer['name']) ?></li>
                            <li><strong>Email:</strong> <?= e($customer['email']) ?></li>
                            <li><strong>Phone:</strong> <?= e($customer['phone']) ?></li>
                            <li><strong>Address:</strong> <?= e($customer['address']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Purchase Summary</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><strong>Period:</strong> <?= $startDate ?> to <?= $endDate ?></li>
                            <li><strong>Total Orders:</strong> <?= $history['summary']['order_count'] ?></li>
                            <li><strong>Total Products:</strong> <?= $history['summary']['product_count'] ?></li>
                            <li><strong>Total Amount:</strong> <?= formatPrice($history['summary']['total_amount']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Orders (<?= $history['summary']['order_count'] ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (empty($history['orders'])): ?>
                    <p class="text-center">No orders found in the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th class="d-print-none">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history['orders'] as $order): ?>
                                    <tr>
                                        <td>#<?= e($order['id']) ?></td>
                                        <td><?= formatDate($order['created_at'], 'Y-m-d H:i') ?></td>
                                        
                                        <td><?= count($order['items']) ?></td>
                                        <td><?= formatPrice($order['total_amount']) ?></td>
                                        <td class="d-print-none">
                                            <a href="<?= baseUrl("/orders/{$order['id']}") ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Products Summary -->
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Products Purchased</h6>
            </div>
            <div class="card-body">
                <?php if (empty($history['products'])): ?>
                    <p class="text-center">No products purchased in the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history['products'] as $product): ?>
                                    <tr>
                                        <td><?= e($product['product_name']) ?></td>
                                        <td><?= e($product['quantity']) ?></td>
                                        <td><?= formatPrice($product['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th></th>
                                    <th><?= formatPrice($history['summary']['total_amount']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none {
            display: none !important;
        }
        .card {
            border: none !important;
        }
        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid #000 !important;
        }
        .badge {
            border: 1px solid #000 !important;
        }
    }
</style>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?> 