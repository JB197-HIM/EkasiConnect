<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Only logged-in sellers can manage their custom inventory dashboard
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch all marketplace rows uploaded by this specific active user session token
$query = "SELECT * FROM products WHERE seller_id = ? ORDER BY product_id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - Seller Control Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar-custom { background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%); }
        .table-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .thumb-container { width: 60px; height: 45px; background-color: #eaeff5; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 6px; }
        .thumb-container img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">Ekasi Connect</a>
            <div class="d-flex gap-2">
                <a class="btn btn-light btn-sm fw-semibold px-3" href="upload.php">+ List Another Item</a>
                <a class="btn btn-outline-light btn-sm px-3" href="index.php">Back to Storefront</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="mb-4">
            <h1 class="fw-bold text-dark mb-1">My Marketplace Inventory</h1>
            <p class="text-muted small">Track, review, or delete items you are offering within the community.</p>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">Your product listing was successfully removed.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'failed'): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">Error: System was unable to process deletion query request.</div>
        <?php endif; ?>

        <div class="card table-card p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th scope="col" style="width: 80px;">Image</th>
                            <th scope="col">Item Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
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
                                        <span class="text-muted small d-inline-block text-truncate" style="max-width: 300px;"><?php echo htmlspecialchars($row['description']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 text-capitalize"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">R <?php echo number_format($row['price'], 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-3 small">Live</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                                           class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-medium"
                                           onclick="return confirm('Are you sure you want to completely delete this item listing? This cannot be undone.');">
                                            Delete Listing
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center py-5 text-muted">You have no active products listed in your dashboard catalog yet.</td></tr>';
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>