<?php
session_start();
require_once 'db_connect.php';

// Security Gate 1: Check if user is logged in as a seller
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Security Gate 2: Secure SQL verification ensuring the item belongs to THIS seller
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $seller_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        
        // --- AUTOMATED AUDIT LOG TRIGGER ---
        $log_action = "PRODUCT_REMOVAL";
        $log_desc = "Seller manually removed their own product item (ID #" . $product_id . ")";
        $log_user = $_SESSION['username'];

        $audit_stmt = $conn->prepare("INSERT INTO audit_logs (action_type, description, performed_by) VALUES (?, ?, ?)");
        $audit_stmt->bind_param("sss", $log_action, $log_desc, $log_user);
        $audit_stmt->execute();
        $audit_stmt->close();

        header("Location: my_listings.php?success=deleted");
    } else {
        // If row wasn't affected, they likely tried deleting an item that wasn't theirs
        header("Location: my_listings.php?error=unauthorized_or_missing");
    }
    $stmt->close();
} else {
    header("Location: my_listings.php");
}
exit();
?>