<?php
/**
 * Common invoice template for both preview and printing
 * 
 * Expected variables:
 * - $order: order information
 * - $items: order items
 * - $isPreview (optional): whether this is a preview or real invoice
 */
?>

<div class="order-header text-center mb-4">
    <h3>INVOICE</h3>
    <div class="company-info">
        <h5>Sales Management System</h5>
        <p>123 Business Street, City, Country</p>
        <p>Email: contact@example.com | Phone: (123) 456-7890</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <h5>Customer Information:</h5>
        <p>
            <strong><?= e($order['customer_name']) ?></strong><br>
            <?php if (!empty($order['customer_address'])): ?>
                <?= e($order['customer_address']) ?><br>
            <?php endif; ?>
            <?php if (!empty($order['customer_email'])): ?>
                Email: <?= e($order['customer_email']) ?><br>
            <?php endif; ?>
            <?php if (!empty($order['customer_phone'])): ?>
                Phone: <?= e($order['customer_phone']) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="col-md-6">
        <h5>Order Information:</h5>
        <p>
            <strong>Order ID:</strong> #<?= e($order['id']) ?><br>
            <?php if (isset($order['created_at'])): ?>
                <strong>Creation Date:</strong> <?= formatDate($order['created_at'], 'Y-m-d H:i') ?><br>
            <?php else: ?>
                <strong>Created At:</strong> <?= isset($isPreview) && $isPreview ? date('Y-m-d H:i') : formatDate($order['created_at'], 'Y-m-d H:i') ?><br>
            <?php endif; ?>
            <?php if (!empty($order['notes'])): ?>
                <strong>Notes:</strong> <?= e($order['notes']) ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($items as $item): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= isset($formatPrice) ? formatPrice($item['unit_price']) : number_format($item['unit_price'], 2) . ' VND' ?></td>
                    <td><?= e($item['quantity']) ?></td>
                    <td>
                        <?php 
                        $subtotal = isset($item['subtotal']) ? $item['subtotal'] : ($item['unit_price'] * $item['quantity']);
                        echo isset($formatPrice) ? formatPrice($subtotal) : number_format($subtotal, 2) . ' VND';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th><?= isset($formatPrice) ? formatPrice($order['total_amount']) : number_format($order['total_amount'], 2) . ' VND' ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="footer text-center mt-4">
    <?php if (isset($isPreview) && $isPreview): ?>
        <p>PREVIEW RECEIPT - NOT A VALID INVOICE</p>
    <?php endif; ?>
    <p>Thank you for your purchase!</p>
    <?php if (!isset($isPreview) || !$isPreview): ?>
        <p>This is an automatically generated invoice and does not require a signature.</p>
        <p>Print Date: <?= date('Y-m-d H:i:s') ?></p>
    <?php endif; ?>
</div> 