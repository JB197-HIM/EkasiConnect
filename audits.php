<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Only let logged-in Admins access this panel
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch all system activities, newest first
$query = "SELECT * FROM audit_logs ORDER BY log_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Audits - Global Administration Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { background: #1e3c72; min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); border-radius: 8px; margin-bottom: 5px; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .table-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        
        /* Dynamic Multi-Severity Badges matching live console view */
        .badge-severity { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.2px; }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <!-- SIDEBAR NAVIGATION CONTAINER - Exactly Matched to admin.php & manage_products.php Layout -->
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
                            <a href="audits.php" class="nav-link active py-2 px-3 fw-medium">Transaction Audits</a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <hr class="bg-white-50 mt-5">
                    <a href="index.php" class="text-warning small d-block mb-2 text-decoration-none text-center">← Back to Website</a>
                    <a href="login.php" class="btn btn-outline-light w-100 btn-sm rounded-pill">Sign Out</a>
                </div>
            </div>

            <!-- MAIN INTERFACE WORKSPACE CONTENT -->
            <div class="col-md-9 col-lg-10 p-md-5 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h1 class="fw-bold text-dark mb-1">System Activity Logs</h1>
                        <p class="text-muted small">Review automated operational footprints and administration actions.</p>
                    </div>
                    <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-medium small tracking-wide shadow-sm" style="background-color: #ffffff !important;">Security Vault</span>
                </div>

                <!-- System Audit Trail Table Container Card -->
                <div class="card table-card p-4 bg-white">
                    <h5 class="fw-bold mb-4 text-dark" style="font-size: 1.15rem; letter-spacing: -0.3px;">System Audit Trail</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th style="width: 90px;">Log ID</th>
                                    <th style="width: 160px;">Action Type</th>
                                    <th>Activity Details</th>
                                    <th>Performed By</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): 
                                        // Dynamic Styling Conditions mapped accurately against database action flags
                                        $action = htmlspecialchars($row['action_type']);
                                        $badge_class = "bg-secondary text-white"; 
                                        
                                        if ($action === 'REGISTRATION') {
                                            $badge_class = "bg-info text-dark";
                                        } elseif ($action === 'PRODUCT_UPLOAD') {
                                            $badge_class = "bg-success text-white";
                                        } elseif ($action === 'PRODUCT_REMOVAL' || $action === 'ADMIN_REMOVAL') {
                                            $badge_class = "bg-danger text-white";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-muted small">#<?php echo htmlspecialchars($row['log_id']); ?></td>
                                            <td>
                                                <span class="badge badge-severity <?php echo $badge_class; ?> px-2.5 py-1.5 rounded">
                                                    <?php echo $action; ?>
                                                </span>
                                            </td>
                                            <td class="text-dark fw-normal"><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td>
                                                <strong class="text-secondary fw-semibold">
                                                    <?php 
                                                        $user = htmlspecialchars($row['performed_by']);
                                                        // Ensure string starts cleanly with a single @ symbol handle cleanly
                                                        echo (strpos($user, '@') === 0) ? $user : '@' . $user; 
                                                    ?>
                                                </strong>
                                            </td>
                                            <td class="text-muted small"><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No system activities recorded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>