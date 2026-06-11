<?php
session_start();
require_once 'db_connect.php';

// Force Admin role verification (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle Role Updates (Update)
if (isset($_POST['update_role'])) {
    $target_user_id = intval($_POST['user_id']);
    $new_role_id = intval($_POST['role_id']);
    
    $update_stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
    $update_stmt->bind_param("ii", $new_role_id, $target_user_id);
    $update_stmt->execute();
    header("Location: manage_users.php?status=updated");
    exit();
}

// Handle User Account Removal (Delete)
if (isset($_GET['delete_id'])) {
    $delete_user_id = intval($_GET['delete_id']);
    
    // Prevent admin from deleting themselves accidentally
    if ($delete_user_id !== $_SESSION['user_id']) {
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $delete_stmt->bind_param("i", $delete_user_id);
        $delete_stmt->execute();
    }
    header("Location: manage_users.php?status=deleted");
    exit();
}

// Fetch all registered profiles along with their textual roles (Display/Read)
$query = "SELECT u.user_id, u.email, r.role_id, r.role_name 
          FROM users u 
          INNER JOIN roles r ON u.role_id = r.role_id 
          ORDER BY u.user_id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekasi Admin - Role-Based Access Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { background-color: #1e293b; min-height: 100vh; color: #fff; }
        .sidebar a { color: #94a3b8; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #334155; border-radius: 8px; }
        .main-content { background-color: #f8fafc; min-height: 100vh; }
        .panel-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .badge-admin { background-color: #e0f2fe; color: #0369a1; }
        .badge-seller { background-color: #fef3c7; color: #b45309; }
        .badge-buyer { background-color: #dcfce7; color: #15803d; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0 sidebar p-4 d-flex flex-column justify-content-between">
            <div>
                <h4 class="fw-bold mb-4 text-white">Ekasi Admin</h4>
                <hr style="border-color: #334155;">
                <div class="d-flex flex-column gap-2 mt-3">
                    <a href="admin.php" class="p-3 fs-6">Dashboard Overview</a>
                    <a href="manage_users.php" class="p-3 fs-6 active fw-medium text-success">User Management (RBAC)</a>
                    <a href="manage_products.php" class="p-3 fs-6">Product Listings</a>
                </div>
            </div>
            <div>
                <a href="index.php" class="text-warning small d-block mb-2">← Back to Website</a>
                <a href="logout.php" class="btn btn-sm btn-outline-danger w-100 rounded-pill">Sign Out</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 main-content p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark">Role-Based Access Control</h2>
                    <p class="text-muted small">Manage platform users, modify roles, and handle member revocations.</p>
                </div>
                <a href="register.php" class="btn btn-success rounded-pill px-4 shadow-sm fw-medium">+ Add New User</a>
            </div>

            <?php if(isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 mb-4" role="alert">
                    Operation completed successfully! Relational mapping updated.
                    <button type="button" class="btn-close" data-bs-dismiss dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card panel-card p-4 bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small fw-bold text-uppercase">
                            <tr>
                                <th class="ps-3">User ID</th>
                                <th>Email Address / Identification</th>
                                <th>Assigned Role</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($user = $result->fetch_assoc()): 
                                    // Assign conditional style classes based on database roles
                                    $badge_class = 'badge-buyer';
                                    if($user['role_id'] == 1) $badge_class = 'badge-admin';
                                    if($user['role_id'] == 2) $badge_class = 'badge-seller';
                                ?>
                                <tr>
                                    <td class="fw-semibold ps-3">#USR-<?php echo str_pad($user['user_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?> px-3 py-2 rounded-pill fw-medium">
                                            <?php echo htmlspecialchars($user['role_name']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-2">
                                            <form action="manage_users.php" method="POST" class="d-inline-flex gap-1 align-items-center">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <select name="role_id" class="form-select form-select-sm rounded-3" style="width: 110px;">
                                                    <option value="1" <?php if($user['role_id'] == 1) echo 'selected'; ?>>Admin</option>
                                                    <option value="2" <?php if($user['role_id'] == 2) echo 'selected'; ?>>Seller</option>
                                                    <option value="3" <?php if($user['role_id'] == 3) echo 'selected'; ?>>Buyer</option>
                                                </select>
                                                <button type="submit" name="update_role" class="btn btn-sm btn-outline-primary rounded-3 px-2">Save</button>
                                            </form>

                                            <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                                <a href="manage_users.php?delete_id=<?php echo $user['user_id']; ?>" 
                                                   onclick="return confirm('Are you sure you want to drop this user profile? All linked item listings will be deleted.');" 
                                                   class="btn btn-sm btn-outline-danger rounded-3 px-3">
                                                    Suspend
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-light text-muted rounded-3 px-3" disabled>Active</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No registered profiles detected in the database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>