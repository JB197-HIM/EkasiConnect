<?php
// Initialize the session context pool
session_start();

// 1. Unset all session global variables 
$_SESSION = array();

// 2. Clear the session cookie from the user's browser if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. Completely destroy the server-side session registry
session_destroy();

// 4. Redirect the user back to the secure login gateway page
header("Location: login.php");
exit();
?>