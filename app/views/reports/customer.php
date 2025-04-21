<?php ob_start(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Customer Purchase Reports</h6>
    </div>
    <div class="card-body">
        <p>Select a customer to view their purchase history.</p>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="customerTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No customers found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= e($customer['id']) ?></td>
                                <td><?= e($customer['name']) ?></td>
                                <td><?= e($customer['email']) ?></td>
                                <td><?= e($customer['phone']) ?></td>
                                <td>
                                    <a href="<?= baseUrl("/reports/customer/{$customer['id']}") ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-chart-line"></i> View Purchases
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('customerTable');
        if (table) {
            new DataTable(table, {
                order: [[0, 'desc']]
            });
        }
    });
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?> 