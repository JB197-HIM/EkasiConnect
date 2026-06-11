<?php
session_start();
require_once 'db_connect.php';

// Security Gate: Verify login status and permissions rule credentials
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $product_id = intval($_GET['id']);
    $seller_id = $_SESSION['user_id'];

    // 1. Fetch item data FIRST to read the image filename before clearing the database entry row
    $select_query = "SELECT image_path FROM products WHERE product_id = ? AND seller_id = ?";
    $select_stmt = $conn->prepare($select_query);
    $select_stmt->bind_param("ii", $product_id, $seller_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        $image_filename = trim($product['image_path']);
        $select_stmt->close();

        // 2. Perform the database deletion
        $delete_query = "DELETE FROM products WHERE product_id = ? AND seller_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $product_id, $seller_id);

        if ($delete_stmt->execute()) {
            
            // 3. PHYSICAL SERVER CLEANUP: If there is a real file on disk, erase it using PHP's unlink()
            if (!empty($image_filename) && $image_filename !== 'default.jpg' && $image_filename !== '0') {
                $physical_file_path = "uploads/" . $image_filename;
                
                if (file_exists($physical_file_path)) {
                    unlink($physical_file_path); // Physically removes file from the htdocs directory storage room
                }
            }

            $delete_stmt->close();
            header("Location: my_listings.php?success=deleted");
            exit();
        } else {
            $delete_stmt->close();
            header("Location: my_listings.php?error=failed");
            exit();
        }
    } else {
        $select_stmt->close();
        header("Location: my_listings.php");
        exit();
    }
} else {
    header("Location: my_listings.php");
    exit();
}
?>