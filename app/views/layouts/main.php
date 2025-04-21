<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(isset($title) ? $title : 'Sale Management System') ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= baseUrl('public/css/style.css') ?>">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= baseUrl('/') ?>">
                <i class="fas fa-store me-2"></i>Sale Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-3 mx-1 hover-light" href="<?= baseUrl('/products') ?>">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link rounded-pill px-3 mx-1 hover-light" href="<?= baseUrl('/customers') ?>">
                            <i class="fas fa-users me-1"></i>Customers
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-3 mx-1 hover-light" href="<?= baseUrl('/orders') ?>">
                            <i class="fas fa-shopping-cart me-1"></i>Orders
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle rounded-pill px-3 mx-1 hover-light" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-area me-1"></i>Reports
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="<?= baseUrl('/reports/sales') ?>">Sales Reports</a></li>
                            <li><a class="dropdown-item py-2" href="<?= baseUrl('/reports/customer') ?>">Customer Purchase Reports</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle rounded-pill px-3 hover-light" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?= e(isset($_SESSION['username']) ? $_SESSION['username'] : 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="<?= baseUrl('/profile') ?>">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2" href="<?= baseUrl('/logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-3 hover-light" href="<?= baseUrl('/login') ?>">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <!-- Flash Messages -->
        <?php if ($flashMessage = flash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= e($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($flashMessage = flash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= e($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($flashMessage = flash('info')): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?= e($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Page Content -->
        <?php if (isset($title)): ?>
            <h1 class="mb-4"><?= e($title) ?></h1>
        <?php endif; ?>
        
        <?= isset($content) ? $content : '' ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Sale Management System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= baseUrl('public/js/script.js') ?>"></script>
</body>
</html> 