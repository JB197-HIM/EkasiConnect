<?php
// Start a session to keep the user logged in across pages
session_start();

// Include our database connection bridge
require_once 'db_connect.php';

$error_message = "";

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Prepare a secure SQL statement to find the user by email
    $stmt = $conn->prepare("SELECT user_id, username, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Save user info into session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        // --- ROLE-BASED ACCESS CONTROL (RBAC) LOGIC ---
        if ($user['role_id'] == 1) {
            // Role ID 1 is Admin -> Redirect to Admin Dashboard
            header("Location: admin.php");
            exit();
        } else {
            // Role ID 2 or 3 is Seller/Buyer -> Redirect to Marketplace Home
            header("Location: index.php");
            exit();
        }
    } else {
        $error_message = "Invalid email address. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ekasi Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Premium linear gradient blending your exact Navy Blue, Emerald Green, and Teal elements */
            background: linear-gradient(135deg, #1e3c72 0%, #11998e 50%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.25);
            width: 100%;
            max-width: 440px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .brand-logo-holder {
            width: 55px; 
            height: 55px; 
            background: #f0f4f8; 
            border-radius: 14px; 
            border: 1px solid rgba(30,60,114,0.1);
        }
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.25rem rgba(30, 60, 114, 0.15);
        }
        .btn-custom-login {
            background-color: #1e3c72;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-custom-login:hover {
            background-color: #152b52;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="brand-logo-holder d-inline-flex align-items-center justify-content-center shadow-sm mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#1e3c72" class="bi bi-shop-window" viewBox="0 0 16 16">
                <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.25 2.25 0 0 1-2.25 2.25H2.25A2.25 2.25 0 0 1 0 5.625v-.255a1.5 1.5 0 0 1 .311-.976l2.66-3.044zm.76 1.15l-2.3 2.63a.5.5 0 0 0-.12.315v.255a1.25 1.25 0 0 0 1.25 1.25h11.5a1.25 1.25 0 0 0 1.25-1.25v-.255a.5.5 0 0 0-.12-.315l-2.3-2.63a.5.5 0 0 0-.38-.175H3.73a.5.5 0 0 0-.38.175z"/>
                <path d="M1 14h14v1H1v-1zm1-5h12v4H2V9zm1 1v2h2v-2H3zm3 0v2h2v-2H6zm3 0v2h2v-2H9zm3 0v2h1v-2h-1z"/>
            </svg>
        </div>
        
        <h3 class="fw-bold tracking-tight text-dark lh-1 mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">
            Ekasi<span style="color: #11998e;">Connect</span>
        </h3>
        <p class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.58rem; letter-spacing: 0.8px;">
            CONNECTING THE KASI ECONOMY, ONE CLICK AT A TIME.
        </p>
        
        <hr class="w-25 mx-auto my-3" style="border-top: 2px solid #1e3c72; opacity: 0.2;">
        
        <h4 class="fw-bold text-dark mb-0 fs-5">Welcome Back</h4>
    </div>

    <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger py-2 small text-center shadow-sm"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Email Address</label>
            <input type="email" name="email" class="form-control py-2 rounded-3" placeholder="admin@ekasi.com" required>
        </div>
        
        <div class="mb-4">
            <label class="form-label small fw-bold text-secondary">Password</label>
            <input type="password" class="form-control py-2 rounded-3" placeholder="••••••••" readonly>
            <span class="text-muted d-block mt-2 lh-sm" style="font-size: 11px; font-style: italic;">
                *Password verification will be mapped alongside hashing arrays next. Use Email only for this test check.
            </span>
        </div>

        <button type="submit" class="btn btn-custom-login w-100 fw-bold py-2 rounded-3 shadow-sm">Sign In</button>
    </form>
</div>

</body>
</html>