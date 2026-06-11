<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Only logged-in sellers (role_id = 2) can upload products
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $seller_id = $_SESSION['user_id'];
    
    // Default fallback name if no file is provided
    $image_filename = "default.jpg"; 

    // 1. Verify or dynamically create the local uploads directory
    $target_directory = "uploads/";
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true);
    }

    // 2. Core File Upload Processing Logic
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $file_name = $_FILES['product_image']['name'];
        $file_tmp = $_FILES['product_image']['tmp_name'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a secure, unique filename to prevent collisions and broken URL spaces
            $image_filename = "img_" . time() . "_" . uniqid() . "." . $file_ext;
            $destination = $target_directory . $image_filename;

            // Execute the physical file transfer to your htdocs project folder
            if (!move_uploaded_file($file_tmp, $destination)) {
                $error_message = "Server Folder Error: Unable to write file to /uploads directory. Check folder write-permissions.";
                $image_filename = "default.jpg"; 
            }
        } else {
            $error_message = "Extension Error: Only JPG, JPEG, PNG, and GIF image files are accepted.";
        }
    } else {
        $upload_error_code = isset($_FILES['product_image']['error']) ? $_FILES['product_image']['error'] : 'Unknown';
        $error_message = "File Selection Error: No image selected or size limit exceeded. PHP Error Code: " . $upload_error_code;
    }

    // 3. Database Insertion (Fixed Parameter Types)
    if (empty($error_message)) {
        if (empty($title) || empty($description) || $price <= 0 || empty($category)) {
            $error_message = "Please fill in all input fields with valid parameters.";
        } else {
            // Prepare statement secure against SQL injections
            $stmt = $conn->prepare("INSERT INTO products (title, description, price, category, image_path, seller_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Available')");
            
            // FIXED: The 5th character 's' ensures $image_filename is saved as a true string, not a '0'.
            $stmt->bind_param("ssdssi", $title, $description, $price, $category, $image_filename, $seller_id);

            if ($stmt->execute()) {
                $success_message = "Excellent! Product listed successfully with its image assets.";

                // --- AUTOMATED AUDIT LOG TRIGGER ---
                $log_action = "PRODUCT_UPLOAD";
                $log_desc = "Listed a new item on the marketplace: " . $title . " for R" . number_format($price, 2);
                $log_user = isset($_SESSION['username']) ? "@" . $_SESSION['username'] : "@Unknown";

                $audit_stmt = $conn->prepare("INSERT INTO audit_logs (action_type, description, performed_by) VALUES (?, ?, ?)");
                $audit_stmt->bind_param("sss", $log_action, $log_desc, $log_user);
                $audit_stmt->execute();
                $audit_stmt->close();
            } else {
                $error_message = "Database Error: Failed to execute system write query.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List an Item - Ekasi Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%); }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">Ekasi Connect</a>
            <a class="btn btn-outline-light btn-sm px-3" href="index.php">Back to Storefront</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card form-card p-4 p-md-5 bg-white">
                    <h2 class="fw-bold text-dark mb-2">List a New Product</h2>
                    <p class="text-muted small mb-4">Provide details and an image to showcase your product perfectly.</p>

                    <?php if(!empty($success_message)): ?>
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Item Name / Title</label>
                            <input type="text" name="title" class="form-control p-3 rounded-3" placeholder="e.g. iPhone 8" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Product Category</label>
                            <select name="category" class="form-select p-3 rounded-3" required>
                                <option value="" disabled selected>Select a category...</option>
                                <option value="Electronics">Electronics & Gadgets</option>
                                <option value="Clothing">Clothing & Fashion</option>
                                <option value="Food">Food & Groceries</option>
                                <option value="Services">Local Services</option>
                                <option value="General">Other / General</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Product Image File</label>
                            <input type="file" name="product_image" class="form-control p-3 rounded-3" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Price (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted px-3">R</span>
                                <input type="number" step="0.01" name="price" class="form-control p-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-secondary small">Full Product Description</label>
                            <textarea name="description" rows="4" class="form-control p-3 rounded-3" placeholder="Describe condition, features..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">Publish Marketplace Listing</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>