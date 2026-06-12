<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Live Production Database Configuration
$host    = 'sql210.infinityfree.com'; 
$db_user = 'if0_42161547'; 
$db_pass = 'idG2ZFkuE1QFv'; 
$db_name = 'if0_42161547_ekasi_db';   

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Critical Failure: " . $conn->connect_error);
}

// Global helper utility to write records to the audit trail dynamically
function log_system_action($conn, $action_type, $action_details) {
    if (isset($_SESSION['user_id'])) {
        $operator_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("INSERT INTO audit_logs (action, description, performed_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $action_type, $action_details, $operator_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>