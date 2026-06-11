<?php
// Enable error reporting to catch any hidden environment issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Inline Database Connection matching your local setup
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "ekasi_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// 1. Fetch current profile data from the database
$query = $conn->prepare("SELECT username, email, role_id FROM users WHERE user_id = ?");
if ($query) {
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    $query->close();
} else {
    die("Database fetch statement failed: " . $conn->error);
}

// 2. Process account setting updates when "Save Changes" is pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['password']; 
    
    if (!empty($email)) {
        // Check if email is already taken by another user profile record
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $error = "This email address is already linked to another profile.";
        } else {
            // Check if the user wants to update their registration password
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE user_id = ?");
                $update->bind_param("ssi", $email, $hashed_password, $user_id);
            } else {
                $update = $conn->prepare("UPDATE users SET email = ? WHERE user_id = ?");
                $update->bind_param("si", $email, $user_id);
            }
            
            if ($update->execute()) {
                $success = "Account profile access settings updated successfully!";
                $user['email'] = $email;
            } else {
                $error = "Failed to update profile settings: " . $update->error;
            }
            $update->close();
        }
        $check->close();
    } else {
        $error = "Email address cannot be submitted empty.";
    }
}

// Determine the human-readable role badge name based on the database role_id
$role_badge = "VERIFIED MEMBER";
$badge_color = "bg-secondary";

if (isset($user['role_id'])) {
    if ($user['role_id'] == 1) {
        $role_badge = "VERIFIED ADMIN";
        $badge_color = "bg-danger";
    } elseif ($user['role_id'] == 2) {
        $role_badge = "VERIFIED SELLER";
        $badge_color = "bg-primary";
    } elseif ($user['role_id'] == 3) {
        $role_badge = "VERIFIED BUYER";
        $badge_color = "bg-success"; // Premium green badge matching your profile mockup
    }
}

// Get the first letter of the username for the profile avatar icon
$avatar_initial = !empty($user['username']) ? strtoupper(substr($user['username'], 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account Profile - Ekasi Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            min-height: 100vh;
            margin: 0;
        }
        /* Premium Navigation Bar matching the Storefront Navbar Layout */
        .premium-navbar {
            background-color: #1e3c72;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand-block {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar-logo-holder {
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar-pill-btn {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .navbar-pill-btn:hover {
            background-color: white;
            color: #1e3c72;
            transform: translateY(-1px);
        }
        
        /* Profile Layout Container */
        .profile-container {
            max-width: 650px;
            margin: 50px auto;
        }
        .premium-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 35px rgba(30, 60, 114, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.01);
            padding: 40px;
        }
        .avatar-circle {
            width: 85px;
            height: 85px;
            background: #e1e7f0;
            color: #1e3c72;
            font-size: 2.2rem;
            font-weight: 700;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            color: #334155;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.25rem rgba(30, 60, 114, 0.12);
        }
        .btn-custom-save {
            background-color: #0061ff;
            color: white;
            font-weight: 600;
            border-radius: 14px;
            padding: 14px;
            border: none;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .btn-custom-save:hover {
            background-color: #0052da;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 97, 255, 0.25);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="premium-navbar">
        <div class="navbar-brand-block">
            <div class="navbar-logo-holder shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#1e3c72" class="bi bi-shop-window" viewBox="0 0 16 16">
                    <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.25 2.25 0 0 1-2.25 2.25H2.25A2.25 2.25 0 0 1 0 5.625v-.255a1.5 1.5 0 0 1 .311-.976l2.66-3.044zm.76 1.15l-2.3 2.63a.5.5 0 0 0-.12.315v.255a1.25 1.25 0 0 0 1.25 1.25h11.5a1.25 1.25 0 0 0 1.25-1.25v-.255a.5.5 0 0 0-.12-.315l-2.3-2.63a.5.5 0 0 0-.38-.175H3.73a.5.5 0 0 0-.38.175z"/>
                    <path d="M1 14h14v1H1v-1zm1-5h12v4H2V9zm1 1v2h2v-2H3zm3 0v2h2v-2H6zm3 0v2h2v-2H9zm3 0v2h1v-2h-1z"/>
                </svg>
            </div>
            <div>
                <h4 class="fw-bold m-0 p-0 lh-1" style="font-size: 1.35rem; letter-spacing: -0.5px;">
                    Ekasi<span style="color: #38ef7d;">Connect</span>
                </h4>
                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.55rem; letter-spacing: 0.5px;">Connecting the kasi economy</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="navbar-pill-btn">Back to Storefront</a>
            <a href="logout.php" class="navbar-pill-btn" style="background-color: rgba(217, 83, 79, 0.1); border-color: rgba(217, 83, 79, 0.2);">Sign Out</a>
        </div>
    </div>

    <div class="container px-3">
        <div class="profile-container">
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger rounded-3 shadow-sm text-center py-2 mb-4 small"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success rounded-3 shadow-sm text-center py-2 mb-4 small"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="premium-card text-center mb-4">
                <div class="avatar-circle shadow-sm">
                    <?php echo htmlspecialchars($avatar_initial); ?>
                </div>
                <h2 class="fw-bold text-dark mb-1">@<?php echo htmlspecialchars($user['username'] ?? 'Trader'); ?></h2>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                <span class="badge <?php echo $badge_color; ?> px-3 py-2 rounded-pill fw-bold text-uppercase small" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                    <?php echo $role_badge; ?>
                </span>
            </div>

            <div class="premium-card">
                <h4 class="fw-bold text-dark mb-4" style="letter-spacing: -0.4px;">Account Access Settings</h4>
                
                <form action="profile.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-secondary fw-bold small mb-2" style="letter-spacing: 0.5px; font-size: 0.75rem;">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-secondary fw-bold small mb-2" style="letter-spacing: 0.5px; font-size: 0.75rem;">Change Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    
                    <button type="submit" class="btn btn-custom-save w-100 shadow-sm mt-2">Save Changes</button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>