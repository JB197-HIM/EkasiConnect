<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Only system administrators can moderate items globally
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch every product in the database along with its respective seller name
$query = "SELECT products.*, users.username FROM products JOIN users ON products.seller_id = users.user_id ORDER BY products.product_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Global Catalog Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .sidebar { background: #1e3c72; min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); border-radius: 8px; margin-bottom: 5px; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .table-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .thumb-container { width: 60px; height: 45px; background-color: #eaeff5; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 6px; }
        .thumb-container img { width: 100%; height: 100%; object-fit: cover; }
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
                            <a href="manage_products.php" class="nav-link active py-2 px-3 fw-medium">Manage Products</a>
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
                <div class="mb-4">
                    <h1 class="fw-bold text-dark mb-1">Global Catalog Moderation</h1>
                    <p class="text-muted small">Review or override any item listing posted across the platform marketplace.</p>
                </div>

                <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">The selected marketplace asset was permanently purged from storage.</div>
                <?php endif; ?>

                <div class="card table-card p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th style="width: 80px;">Image</th>
                                    <th>Item Details</th>
                                    <th>Seller</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): 
                                        $image_filename = trim($row['image_path']);
                                        $image_src = "uploads/" . $image_filename;
                                        $has_valid_image = (!empty($image_filename) && $image_filename !== 'default.jpg' && $image_filename !== '0');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="thumb-container border shadow-sm">
                                                    <?php if ($has_valid_image): ?>
                                                        <img src="<?php echo $image_src; ?>" alt="Preview">
                                                    <?php else: ?>
                                                        <span style="font-size: 1.1rem;">📦</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($row['title']); ?></h6>
                                                <span class="text-muted small d-inline-block text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($row['description']); ?></span>
                                            </td>
                                            <td><span class="text-secondary fw-medium small">@<?php echo htmlspecialchars($row['username']); ?></span></td>
                                            <td><span class="badge bg-light text-secondary border rounded-pill px-3 py-1"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                            <td><span class="fw-bold text-dark">R <?php echo number_format($row['price'], 2); ?></span></td>
                                            <td class="text-end">
                                                <a href="product_delete_admin.php?id=<?php echo $row['product_id']; ?>" 
                                                   class="btn btn-outline-danger btn-sm rounded-pill px-3 small fw-medium"
                                                   onclick="return confirm('As administrator, are you sure you want to override and delete this listing?');">
                                                     Remove Listing
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No products have been uploaded to the database yet.</td></tr>
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