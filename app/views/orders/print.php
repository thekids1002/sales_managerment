<?php 
// Standalone print template (no layout)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($isDraft) && $isDraft ? "Draft Order" : "Order" ?> #<?= e($order['id']) ?> - Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .order-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .order-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-info, .order-info {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
        .draft-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(200, 200, 200, 0.3);
            z-index: -1;
            pointer-events: none;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-end mb-3">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
    
    <?php if (isset($isDraft) && $isDraft): ?>
    <div class="draft-watermark">DRAFT</div>
    <?php endif; ?>
    
    <div class="order-header">
        <h1><?= isset($isDraft) && $isDraft ? "DRAFT INVOICE" : "INVOICE" ?></h1>
        <div class="company-info">
            <h3>Sale Management System</h3>
            <p>123 Business Street, City, Country</p>
            <p>Email: contact@example.com | Phone: (123) 456-7890</p>
        </div>
    </div>
    
    <div class="order-meta">
        <div class="customer-info">
            <h5>Bill To:</h5>
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
        <div class="order-info">
            <h5>Order Information:</h5>
            <p>
                <?php if (isset($isDraft) && $isDraft): ?>
                <strong>Order ID:</strong> #<?= e($order['id']) ?><br>
                <strong>Created:</strong> <?= formatDate($order['created_at'], 'Y-m-d H:i') ?><br>
                <?php if (!empty($order['updated_at'])): ?>
                <strong>Last Updated:</strong> <?= formatDate($order['updated_at'], 'Y-m-d H:i') ?><br>
                <?php endif; ?>
                <?php else: ?>
                <strong>Order Number:</strong> #<?= e($order['id']) ?><br>
                <strong>Date:</strong> <?= formatDate($order['order_date'], 'Y-m-d H:i') ?><br>
                <?php endif; ?>
                <?php if (!empty($order['notes'])): ?>
                <strong>Notes:</strong> <?= e($order['notes']) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($items as $item): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= formatPrice($item['unit_price']) ?></td>
                    <td><?= e($item['quantity']) ?></td>
                    <td>
                        <?php if (isset($isDraft) && $isDraft && isset($item['subtotal'])): ?>
                            <?= formatPrice($item['subtotal']) ?>
                        <?php else: ?>
                            <?= formatPrice($item['unit_price'] * $item['quantity']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="totals">
        <table>
            <tr>
                <th>Total:</th>
                <td><?= formatPrice($order['total_amount']) ?></td>
            </tr>
        </table>
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="footer">
        <?php if (isset($isDraft) && $isDraft): ?>
        <p>THIS IS A DRAFT INVOICE - NOT A VALID RECEIPT</p>
        <?php endif; ?>
        <p>Thank you for your business!</p>
        <p><?= isset($isDraft) && $isDraft ? '' : 'This is a computer-generated invoice and does not require a signature.' ?></p>
        <p>Printed on: <?= date('Y-m-d H:i:s') ?></p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html> 