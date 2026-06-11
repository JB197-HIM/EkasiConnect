<?php
session_start();
require_once 'db_connect.php';

// Security Gate: ONLY system administrators (role_id = 1) can execute this script
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $product_id = intval($_GET['id']);
    $admin_username = $_SESSION['username'] ?? 'Admin';

    // 1. Fetch item data FIRST to read the image filename before clearing the database record
    $select_query = "SELECT title, image_path FROM products WHERE product_id = ?";
    $select_stmt = $conn->prepare($select_query);
    $select_stmt->bind_param("i", $product_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        $product_title = $product['title'];
        $image_filename = trim($product['image_path']);
        $select_stmt->close();

        // 2. Global Administrative Override Deletion Query (No seller_id restriction)
        $delete_query = "DELETE FROM products WHERE product_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $product_id);

        if ($delete_stmt->execute()) {
            
            // 3. PHYSICAL STORAGE CLEANUP: Erase the file from the htdocs uploads folder
            if (!empty($image_filename) && $image_filename !== 'default.jpg' && $image_filename !== '0') {
                $physical_file_path = "uploads/" . $image_filename;
                
                if (file_exists($physical_file_path)) {
                    unlink($physical_file_path); // Permanently purges the file from disk storage
                }
            }
            $delete_stmt->close();

            // 4. AUDIT TRAIL LOGGING: Record this admin action into the audit_logs table
            $log_action = "ADMIN_REMOVAL";
            $log_desc = "Administrator override: Permanently removed product listing '" . $product_title . "' (ID #" . $product_id . ") due to moderation.";
            
            $log_query = "INSERT INTO audit_logs (action_type, description, performed_by) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_query);
            $log_stmt->bind_param("sss", $log_action, $log_desc, $admin_username);
            $log_stmt->execute();
            $log_stmt->close();

            // Redirect back to the global admin list with a success notification
            header("Location: manage_products.php?success=deleted");
            exit();
        } else {
            $delete_stmt->close();
            header("Location: manage_products.php?error=failed");
            exit();
        }
    } else {
        $select_stmt->close();
        header("Location: manage_products.php");
        exit();
    }
} else {
    header("Location: manage_products.php");
    exit();
}
?>