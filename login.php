<?php
// Force display of error messages right on the screen to replace the 500 error page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start a session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include our database connection bridge
require_once 'db_connect.php';

$error_message = "";

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password']; 

    if (!empty($email) && !empty($password)) {
        // Secure SQL statement to look up user data
        $stmt = $conn->prepare("SELECT user_id, username, password, role_id FROM users WHERE email = ?");
        if (!$stmt) {
            die("SQL Preparation Fault: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Password verification check matching your plain text setup
            if ($password === $user['password']) {
                // Initialize session values fully before redirecting
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                // SAFEGUARD: Try logging the system action, but don't break the app if the audit table isn't ready
                try {
                    if (function_exists('log_system_action')) {
                        log_system_action($conn, "Authentication", "User logged into live platform environment.");
                    }
                } catch (Exception $e) {
                    // Quietly ignore log faults to let the user log in anyway
                }

                $stmt->close(); // Close statement before redirecting

                // --- ROLE-BASED ACCESS CONTROL (RBAC) LOGIC ---
                if (intval($user['role_id']) === 1) {
                    header("Location: admin.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $error_message = "Authentication failure: Incorrect account password credentials.";
            }
        } else {
            $error_message = "Invalid email address. Please try again.";
        }
        if (isset($stmt) && $stmt === false) { $stmt->close(); }
    } else {
        $error_message = "Please complete all validation profile elements.";
    }
}
?>