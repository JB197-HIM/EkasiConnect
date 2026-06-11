<?php
session_start();

// 1. Security Gate: Only let logged-in Admins execute deletions
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

// 2. Check if a specific user ID was sent through the URL link
if (isset($_GET['id'])) {
    $user_to_delete = intval($_GET['id']);

    // Prevent the Admin from accidentally deleting themselves!
    if ($user_to_delete === $_SESSION['user_id']) {
        header("Location: admin.php?error=cannot_delete_self");
        exit();
    }

    // 3. Prepare a secure SQL statement to remove the targeted account row
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_to_delete);
    
    if ($stmt->execute()) {
        header("Location: admin.php?success=user_removed");
    } else {
        header("Location: admin.php?error=delete_failed");
    }
    $stmt->close();
} else {
    // If no ID was provided, just send them back to the dashboard safely
    header("Location: admin.php");
}
exit();
?>