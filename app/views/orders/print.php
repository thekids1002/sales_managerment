<?php 
// Standalone print template (no layout)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= e($order['id']) ?> - Print</title>
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
    
    <div class="order-header">
        <h1>INVOICE</h1>
        <div class="company-info">
            <h3>Sales Management System</h3>
            <p>123 Business Street, City, Country</p>
            <p>Email: contact@example.com | Phone: (123) 456-7890</p>
        </div>
    </div>
    
    <div class="order-meta">
        <div class="customer-info">
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
        <div class="order-info">
            <h5>Order Information:</h5>
            <p>
                <strong>Order ID:</strong> #<?= e($order['id']) ?><br>
                <strong>Creation Date:</strong> <?= formatDate($order['order_date'], 'Y-m-d H:i') ?><br>
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
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($items as $item): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= formatPrice($item['unit_price']) ?></td>
                    <td><?= e($item['quantity']) ?></td>
                    <td><?= formatPrice($item['unit_price'] * $item['quantity']) ?></td>
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
        <p>Thank you for your purchase!</p>
        <p>This is an automatically generated invoice and does not require a signature.</p>
        <p>Print Date: <?= date('Y-m-d H:i:s') ?></p>
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