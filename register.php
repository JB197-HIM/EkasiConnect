<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    // Capture the chosen role dynamically from the form (2 for Seller, 3 for Buyer)
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : 3; 
    
    if (!empty($username) && !empty($email) && !empty($password) && ($role_id == 2 || $role_id == 3)) {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $error = "Account registration conflict: Username or Email already exists.";
        } else {
            // SECURE HASHING: Encrypts the password securely before entering the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // CORRECTED DATABASE QUERY: targets 'password_hash' to align with the production schema
            $insert = $conn->prepare("INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
            $insert->bind_param("sssi", $username, $email, $hashed_password, $role_id);
            
            if ($insert->execute()) {
                $success = "Registration complete! You can now log into your profile.";
            } else {
                $error = "Database insertion error. Please try again.";
            }
            $insert->close();
        }
        $check->close();
    } else {
        $error = "Please fill out all mandatory fields and select an account type.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Ekasi Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #11998e 50%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.25);
            width: 100%;
            max-width: 460px;
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
        
        /* Modern Segmented Toggle Control for Roles */
        .role-selector {
            display: flex;
            background: #f0f4f8;
            padding: 4px;
            border-radius: 12px;
            border: 1px solid rgba(30, 60, 114, 0.08);
        }
        .role-option {
            flex: 1;
            text-align: center;
        }
        .role-option input[type="radio"] {
            display: none;
        }
        .role-option label {
            display: block;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            color: #4a5568;
            transition: all 0.2s ease;
        }
        .role-option input[type="radio"]:checked + label {
            background: #1e3c72;
            color: white;
            box-shadow: 0 4px 10px rgba(30, 60, 114, 0.2);
        }

        .btn-custom-register {
            background-color: #1e3c72;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-custom-register:hover {
            background-color: #152b52;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="register-card my-4">
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
        
        <h4 class="fw-bold text-dark mb-0 fs-5">Join the Marketplace</h4>
        <p class="text-muted small mb-0">Create your unified digital trader profile</p>
    </div>

    <?php if(!empty($error)): ?> 
        <div class="alert alert-danger py-2 small text-center shadow-sm"><?php echo $error; ?></div> 
    <?php endif; ?>
    
    <?php if(!empty($success)): ?> 
        <div class="alert alert-success py-2 small text-center shadow-sm"><?php echo $success; ?></div> 
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary d-block mb-2">I want to register as a:</label>
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="role_buyer" name="role_id" value="3" checked>
                    <label for="role_buyer">🛒 Buyer</label>
                </div>
                <div class="role-option">
                    <input type="radio" id="role_seller" name="role_id" value="2">
                    <label for="role_seller">💼 Seller</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Username</label>
            <input type="text" name="username" class="form-control py-2 rounded-3" placeholder="e.g., Sipho_Trader" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Email Address</label>
            <input type="email" name="email" class="form-control py-2 rounded-3" placeholder="name@domain.co.za" required>
        </div>
        
        <div class="mb-4">
            <label class="form-label small fw-bold text-secondary">Password</label>
            <input type="password" name="password" class="form-control py-2 rounded-3" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-custom-register w-100 fw-bold py-2 rounded-3 shadow-sm mb-2">Generate Profile</button>
    </form>
    
    <div class="text-center mt-3">
        <small class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none fw-semibold" style="color: #1e3c72;">Login here</a></small>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const registrationForm = document.querySelector("form[action='register.php']");
    
    if (registrationForm) {
        registrationForm.addEventListener("submit", function (event) {
            const passwordField = document.querySelector("input[name='password']");
            const usernameField = document.querySelector("input[name='username']");
            const passwordValue = passwordField.value;
            const usernameValue = usernameField.value;

            // Security Rule 1: Password cannot match the username string
            if (passwordValue.toLowerCase() === usernameValue.toLowerCase()) {
                event.preventDefault(); 
                alert("❌ Registration Error: Security constraints require your password to be different from your username.");
                passwordField.focus();
                return false;
            }

            // Security Rule 2: Enforce a secure password length
            if (passwordValue.length < 6) {
                event.preventDefault(); 
                alert("❌ Registration Error: Password must be at least 6 characters long.");
                passwordField.focus();
                return false;
            }
        });
    }
});
</script>

</body>
</html>