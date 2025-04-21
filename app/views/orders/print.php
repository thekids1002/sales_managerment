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
        /* Paper size for A4 */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            font-size: 12pt;
        }

        .container {
            max-width: 100%;
            padding: 0;
        }

        .order-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-info {
            margin-bottom: 15px;
        }
        
        .company-info h5 {
            margin-bottom: 5px;
        }
        
        .company-info p {
            margin-bottom: 4px;
        }

        /* Modified to use grid system for better control */
        .customer-order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Fix width issues */
        .table-responsive {
            overflow-x: visible;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            font-size: 11pt;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        
        /* Adjust column widths */
        th:nth-child(1), td:nth-child(1) { width: 8%; }  /* No. */
        th:nth-child(2), td:nth-child(2) { width: 45%; } /* Product */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Unit Price */
        th:nth-child(4), td:nth-child(4) { width: 12%; } /* Quantity */
        th:nth-child(5), td:nth-child(5) { width: 20%; } /* Total */

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
            page-break-inside: avoid;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .row {
                display: flex;
                flex-wrap: nowrap;
            }
            
            /* Fix for bootstrap container */
            .container {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div class="no-print text-end mb-3 p-3">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="container">
        <div class="invoice-wrapper">
            <?php 
            // We need to define formatPrice for the template
            $formatPrice = true;
            // Override the template's customer and order info section
            $useCustomLayout = true;
            
            // Custom header section
            ?>
            <div class="order-header">
                <h3 style="margin-bottom: 10px;">INVOICE</h3>
                <div class="company-info">
                    <h5 style="margin-bottom: 5px;">Sales Management System</h5>
                    <p style="margin-bottom: 2px;">123 Business Street, City, Country</p>
                    <p>Email: contact@example.com | Phone: (123) 456-7890</p>
                </div>
            </div>

            <div class="customer-order-info">
                <div>
                    <h5>Customer Information:</h5>
                    <p style="margin-bottom: 3px;">
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
                <div>
                    <h5>Order Information:</h5>
                    <p style="margin-bottom: 3px;">
                        <strong>Order ID:</strong> #<?= e($order['id']) ?><br>
                        <strong>Creation Date:</strong> <?= formatDate($order['order_date'], 'Y-m-d H:i') ?><br>
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
                                <td><?= formatPrice($item['unit_price']) ?></td>
                                <td><?= e($item['quantity']) ?></td>
                                <td>
                                    <?php 
                                    $subtotal = isset($item['subtotal']) ? $item['subtotal'] : ($item['unit_price'] * $item['quantity']);
                                    echo formatPrice($subtotal);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th><?= formatPrice($order['total_amount']) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="footer">
                <p>Thank you for your purchase!</p>
                <p>This is an automatically generated invoice and does not require a signature.</p>
                <p>Print Date: <?= date('Y-m-d H:i:s') ?></p>
            </div>
        </div>
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