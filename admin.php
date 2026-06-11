<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Only system administrators (role_id = 1) can access this control engine
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// 1. Fetch KPI Metrics Summary Counters
$user_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$product_count = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_value = $conn->query("SELECT SUM(price) FROM products")->fetch_row()[0];

// 2. Fetch Recent System Activity Audit Logs (Using created_at column)
$log_result = $conn->query("SELECT * FROM audit_logs ORDER BY log_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ekasi Connect Control Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { background: #1e3c72; min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); border-radius: 8px; margin-bottom: 5px; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .stat-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-3 col-lg-2 sidebar p-4 shadow d-flex flex-column justify-content-between">
                <div>
                    <div class="mb-4 pt-2">
                        <span class="fw-bold tracking-tight text-white fs-4 d-block" style="letter-spacing: -0.5px;">
                            Ekasi<span style="color: #38ef7d;">Connect</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.65rem; text-transform: uppercase; display:block; margin-top:2px;">Control Console</span>
                    </div>
                    <hr class="bg-white-50">
                    
                    <ul class="nav flex-column mb-auto gap-1">
    <li>
        <a href="admin.php" class="nav-link py-2 px-3 fw-medium">Dashboard Overview</a>
    </li>
    <li>
        <a href="manage_users.php" class="nav-link py-2 px-3 fw-medium">User Management (RBAC)</a>
    </li>
    <li>
        <a href="manage_products.php" class="nav-link py-2 px-3 fw-medium">Manage Products</a>
    </li>
    <li>
        <a href="audits.php" class="nav-link py-2 px-3 fw-medium">Transaction Audits</a>
    </li>
</ul>
                </div>
                
                <div>
                    <hr class="bg-white-50 mt-5">
                    <a href="index.php" class="text-warning small d-block mb-2 text-decoration-none text-center">← Back to Website</a>
                    <a href="login.php" class="btn btn-outline-light w-100 btn-sm rounded-pill">Sign Out</a>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 p-md-5 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="fw-bold text-dark mb-1">Overview Dashboard</h1>
                        <p class="text-muted small">Global market monitoring and administrative system logs.</p>
                    </div>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 fw-semibold">View Live Storefront</a>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card stat-card p-4 bg-white border-start border-primary border-4">
                            <span class="text-uppercase text-secondary fw-semibold small tracking-wider">Total Active Users</span>
                            <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo $user_count; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-4 bg-white border-start border-success border-4">
                            <span class="text-uppercase text-secondary fw-semibold small tracking-wider">Total Live Items</span>
                            <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo $product_count; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card p-4 bg-white border-start border-warning border-4">
                            <span class="text-uppercase text-secondary fw-semibold small tracking-wider">Marketplace Circulation</span>
                            <h2 class="fw-bold text-dark mt-2 mb-0">R <?php echo number_format($total_value ? $total_value : 0, 2); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">Recent System Audit Operations</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th>Action Type</th>
                                    <th>Description Details</th>
                                    <th>Performed By</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($log_result && $log_result->num_rows > 0): ?>
                                    <?php while($log = $log_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary-subtle text-secondary rounded px-2.5 py-1 text-uppercase font-monospace" style="font-size:0.75rem;"><?php echo htmlspecialchars($log['action_type']); ?></span></td>
                                            <td class="text-dark small"><?php echo htmlspecialchars($log['description']); ?></td>
                                            <td class="fw-medium text-secondary small"><?php echo htmlspecialchars($log['performed_by']); ?></td>
                                            <td class="text-muted small"><?php echo $log['created_at']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted small">No audit operations captured in logs yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

<footer class="bg-white border-top py-4 text-center mt-5 text-secondary small">
        <div class="container">
            &copy; <?php echo date("Y"); ?> EkasiConnect Platform. Built to Empower Local Township Enterprises.
        </div>
    </footer>

</body>
</html>