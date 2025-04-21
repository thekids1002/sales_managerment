<?php ob_start(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sales Reports</h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="get" action="<?= baseUrl('/reports/sales') ?>" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= e($startDate) ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= e($endDate) ?>">
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">Group By</label>
                    <select class="form-select" id="group_by" name="group_by">
                        <option value="day" <?= $groupBy == 'day' ? 'selected' : '' ?>>Day</option>
                        <option value="month" <?= $groupBy == 'month' ? 'selected' : '' ?>>Month</option>
                        <option value="year" <?= $groupBy == 'year' ? 'selected' : '' ?>>Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
    
        <!-- Sales Summary -->
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Sales
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $totalSales = 0;
                                    foreach ($salesSummary as $summary) {
                                        $totalSales += $summary['total_sales'];
                                    }
                                    echo formatPrice($totalSales);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Orders
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $totalOrders = 0;
                                    foreach ($salesSummary as $summary) {
                                        $totalOrders += $summary['order_count'];
                                    }
                                    echo $totalOrders;
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Average Order Value
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalOrders > 0 ? formatPrice($totalSales / $totalOrders) : formatPrice(0) ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sales Data Table -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped" id="salesTable">
                <thead>
                    <tr>
                        <th style="width: 150px">Date</th>
                        <th style="width: 120px">Order Count</th>
                        <th style="width: 150px">Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesSummary as $summary): ?>
                        <tr>
                            <td class="text-truncate" style="max-width: 150px"><?= e($summary['date_group']) ?></td>
                            <td class="text-truncate" style="max-width: 120px"><?= e($summary['order_count']) ?></td>
                            <td class="text-truncate" style="max-width: 150px"><?= formatPrice($summary['total_sales']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Orders in Period -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Orders in Selected Period</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="ordersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total</th>
                       
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found for this period</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= e($order['id']) ?></td>
                                <td><?= formatDate($order['order_date'], 'Y-m-d H:i') ?></td>
                                <td><?= e($order['customer_name']) ?></td>
                                <td><?= formatPrice($order['total_amount']) ?></td>
                               
                                <td>
                                    <a href="<?= baseUrl("/orders/{$order['id']}") ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        new DataTable('#salesTable');
        new DataTable('#ordersTable', {
            order: [[0, 'desc']]
        });
        
        // Chart data
        const salesData = <?= json_encode($salesSummary) ?>;
        const labels = salesData.map(item => item.date_group);
        const sales = salesData.map(item => item.total_sales);
        const orders = salesData.map(item => item.order_count);
        
        // Create chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sales',
                        data: sales,
                        backgroundColor: 'rgba(78, 115, 223, 0.2)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        data: orders,
                        type: 'line',
                        fill: false,
                        borderColor: 'rgba(28, 200, 138, 1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Order Count'
                        }
                    }
                }
            }
        });
    });
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?> 