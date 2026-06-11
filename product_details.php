<?php
session_start();
require_once 'db_connect.php';

// Check if a valid product ID was passed in the URL string
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details along with the seller's username and contact information
$query = "SELECT products.*, users.username, users.email 
          FROM products 
          JOIN users ON products.seller_id = users.user_id 
          WHERE products.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// --- THE WHATSAPP HOOK TWEAK CONFIGURATION ---
// Fallback local mock number for deployment verification (+27612345678)
$seller_phone = "+27612345678"; 

// Create a professional, pre-filled local text message matching your brand identity
$raw_message = "Hi @" . $product['username'] . ", I am interested in your item listing '" . $product['title'] . "' for R " . number_format($product['price'], 2) . " on Ekasi Connect. Is it still available?";

// URL-encode the string so spaces and characters don't break the web link browser format
$encoded_message = urlencode($raw_message);
$whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $seller_phone) . "?text=" . $encoded_message;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - Ekasi Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background: #1e3c72; }
        .detail-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .img-display { background-color: #eaeff5; border-radius: 15px; min-height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .img-display img { width: 100%; height: 100%; object-fit: cover; }
        .whatsapp-btn { background-color: #25D366; color: white; font-weight: 600; border: none; transition: background-color 0.2s; }
        .whatsapp-btn:hover { background-color: #128C7E; color: white; }
    </style>
</head>
<body>

    <!-- NAVIGATION HEADER WITH VISUAL LOGO -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container">
            <!-- BRAND LOGO COMPONENT -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php" style="text-decoration: none;">
                <div class="me-3 d-inline-flex align-items-center justify-content-center shadow-sm" 
                     style="width: 45px; height: 45px; background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%); border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#1e3c72" class="bi bi-shop-window" viewBox="0 0 16 16">
                        <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.25 2.25 0 0 1-2.25 2.25H2.25A2.25 2.25 0 0 1 0 5.625v-.255a1.5 1.5 0 0 1 .311-.976l2.66-3.044zm.76 1.15l-2.3 2.63a.5.5 0 0 0-.12.315v.255a1.25 1.25 0 0 0 1.25 1.25h11.5a1.25 1.25 0 0 0 1.25-1.25v-.255a.5.5 0 0 0-.12-.315l-2.3-2.63a.5.5 0 0 0-.38-.175H3.73a.5.5 0 0 0-.38.175z"/>
                        <path d="M1 14h14v1H1v-1zm1-5h12v4H2V9zm1 1v2h2v-2H3zm3 0v2h2v-2H6zm3 0v2h2v-2H9zm3 0v2h1v-2h-1z"/>
                    </svg>
                </div>
                <div class="d-flex flex-column text-start">
                    <span class="fw-bold tracking-tight text-white lh-1" style="font-size: 1.4rem; letter-spacing: -0.5px;">
                        Ekasi<span style="color: #38ef7d;">Connect</span>
                    </span>
                    <small class="text-white-50 fw-normal tracking-wide mt-1" style="font-size: 0.62rem; letter-spacing: 0.8px; opacity: 0.85;">
                        CONNECTING THE KASI ECONOMY, ONE CLICK AT A TIME.
                    </small>
                </div>
            </a>
            <a href="index.php" class="btn btn-light rounded-pill btn-sm px-4 fw-medium text-dark">Back to Storefront</a>
        </div>
    </nav>

    <!-- CONTENT DISPLAY WORKSPACE -->
    <div class="container my-5">
        <div class="card detail-card p-4 p-md-5 bg-white">
            <div class="row g-5">
                
                <!-- Column 1: Image Showcase Area -->
                <div class="col-md-6">
                    <div class="img-display">
                        <?php 
                        $img_file = trim($product['image_path']);
                        if (!empty($img_file) && $img_file !== 'default.jpg' && $img_file !== '0'): 
                        ?>
                            <img src="uploads/<?php echo $img_file; ?>" alt="Product Asset View">
                        <?php else: ?>
                            <span class="text-muted fs-1">📦 No Image Provided</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Structural Specs & Communication Trigger -->
                <div class="col-md-6 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-primary-subtle text-primary text-uppercase px-3 py-1.5 rounded-pill mb-3 small fw-semibold tracking-wider">
                            <?php echo htmlspecialchars($product['category']); ?>
                        </span>
                        <h1 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($product['title']); ?></h1>
                        <h2 class="fw-bold mb-4" style="color: #1e3c72;">R <?php echo number_format($product['price'], 2); ?></h2>
                        
                        <hr>
                        
                        <h6 class="text-uppercase text-secondary small fw-bold tracking-wider mb-2">Item Description</h6>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="p-3 bg-light rounded-3 mb-4">
                            <small class="text-muted d-block mb-1">Listed by verified seller:</small>
                            <span class="fw-bold text-dark">@<?php echo htmlspecialchars($product['username']); ?></span>
                            <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.75rem;">Secure ID Ref: #004<?php echo $product['product_id']; ?></small>
                        </div>
                    </div>

                    <!-- DYNAMIC WHATSAPP COMMUNICATION TRIGGER ACTION BUTTON -->
                    <div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn whatsapp-btn w-100 rounded-pill py-3 d-flex align-items-center justify-content-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-whatsapp me-2" viewBox="0 0 16 16">
                                  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c1.1-4.312 4.37-6.574 6.574-6.574a6.56 6.56 0 0 1 4.645 1.93 6.56 6.56 0 0 1 1.921 4.646c-.002 3.596-2.92 6.513-6.516 6.513zm3.613-4.633c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                </svg>
                                Contact Seller via WhatsApp
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-secondary w-100 rounded-pill py-3 fw-medium">
                                Sign in to Contact Seller
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>
</html>