<?php
session_start();
// No direct database content mutations are required on this static structural overview page, 
// but session state is verified to align the premium navbar components accurately.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EkasiConnect Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; color: #334155; }
        .navbar-custom { background: #1e3c72; }
        
        /* Premium Hero Banner Custom Styles */
        .hero-banner {
            background: linear-gradient(135deg, #1e3c72 0%, #152b52 100%);
            border-radius: 0 0 40px 40px;
            color: #ffffff;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
        }
        .badge-empower {
            background-color: rgba(56, 239, 125, 0.15);
            color: #38ef7d;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 8px 18px;
            border-radius: 30px;
            display: inline-block;
        }

        /* Stats Banner Design */
        .stats-wrapper {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .stat-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(30, 60, 114, 0.08);
            border: 1px solid rgba(30, 60, 114, 0.03);
        }

        /* Persona Community Cards */
        .persona-card {
            border: none;
            border-radius: 24px;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .persona-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.1);
        }
        .icon-shape {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        
        /* Value Cards Grid */
        .value-item {
            padding: 20px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        /* Premium Register Button Styling to match index.php */
        .btn-register-premium {
            background-color: #38ef7d;
            border-color: #38ef7d;
            color: #1e3c72 !important;
        }
        .btn-register-premium:hover {
            background-color: #2ed56e;
            border-color: #2ed56e;
            box-shadow: 0 4px 12px rgba(56, 239, 125, 0.3);
        }
    </style>
</head>
<body>

    <!-- Premium Navbar - Perfectly Matched to index.php Workspace Structure -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container">
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
            
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-outline-light rounded-pill btn-sm px-4 me-2 fw-medium" style="border: 1px solid rgba(255, 255, 255, 0.35); background-color: rgba(255, 255, 255, 0.05);">← Back to Marketplace</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-outline-light rounded-pill btn-sm px-4">My Account</a>
                    
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                        <a href="admin.php" class="btn btn-warning rounded-pill btn-sm px-3 fw-medium">Admin Panel</a>
                    <?php elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
                        <a href="my_listings.php" class="btn btn-light rounded-pill btn-sm px-3 fw-medium text-dark">Seller Studio</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light rounded-pill btn-sm px-4 fw-medium text-white" style="border: 1px solid rgba(255, 255, 255, 0.35); background-color: rgba(255, 255, 255, 0.1);">Sign In</a>
                    <a href="register.php" class="btn btn-register-premium rounded-pill btn-sm px-4 fw-bold shadow-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Premium Hero Header Block -->
    <div class="hero-banner text-center shadow-sm">
        <div class="container m-auto" style="max-width: 850px;">
            <div class="badge-empower mb-3">Our Story</div>
            <h1 class="display-4 fw-bold text-white mb-3" style="letter-spacing: -1px; font-weight: 700;">Bringing the Kasi Economy Online</h1>
            <p class="lead text-white-50 fw-normal fs-5 mx-md-5 lh-base">
                We believe that the heartbeat of our communities lies within local enterprises. EkasiConnect was created to give township entrepreneurs the visibility and digital tools they deserve to thrive in a modern marketplace.
            </p>
        </div>
    </div>

    <!-- Live Platform Statistics Panel (Inspired by real classified/marketplace structures) -->
    <div class="container stats-wrapper">
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <h2 class="fw-bold text-dark mb-1" style="color: #1e3c72 !important;">150+</h2>
                    <small class="text-uppercase fw-semibold tracking-wider text-muted style-font" style="font-size: 0.7rem;">Verified Vendors</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <h2 class="fw-bold text-success mb-1">1.2k+</h2>
                    <small class="text-uppercase fw-semibold tracking-wider text-muted" style="font-size: 0.7rem;">Local Listings</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <h2 class="fw-bold text-warning mb-1">98%</h2>
                    <small class="text-uppercase fw-semibold tracking-wider text-muted" style="font-size: 0.7rem;">Community Trust</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Workspace -->
    <div class="container my-5 py-4">
        
        <!-- Section 1: Narrative Story Split Layout -->
        <div class="row align-items-center g-5 mb-5 pb-5">
            <div class="col-lg-6">
                <span class="text-uppercase tracking-wider text-primary fw-bold small">Why We Started</span>
                <h2 class="fw-bold text-dark mt-2 mb-4" style="letter-spacing: -0.5px;">Connecting Talent with Opportunity</h2>
                <p class="text-secondary lh-lg mb-3">
                    Every day, incredible work is done in our townships by local mechanics, clothing designers, caterers, and skilled tradespeople. Yet, because these businesses often operate informally, discovering them usually depends entirely on lucky word-of-mouth. 
                </p>
                <p class="text-secondary lh-lg mb-0">
                    <strong>EkasiConnect is changing that story.</strong> We provide a centralized, safe, and beautiful digital storefront hub where local entrepreneurs can list their offerings, share their service locations, and gain professional credibility across the entire neighborhood instantly.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-white border rounded-4 p-5 shadow-sm">
                    <h4 class="fw-bold text-dark mb-4">Our Core Commitments</h4>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="text-primary fs-3 me-3">🤝</div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Hyper-Local Focus</h6>
                            <p class="text-muted small mb-0">We explicitly keep our services focused on local growth, making sure neighborhood income stays within the community.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="text-success fs-3 me-3">🛡️</div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Safety &amp; Transparency</h6>
                            <p class="text-muted small mb-0">Every listing and vendor account goes through structured validation workflows to maintain community safety.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="text-warning fs-3 me-3">✨</div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Zero Cost Entry</h6>
                            <p class="text-muted small mb-0">No complicated setup or hidden operational costs for our micro-enterprises to get their products seen online.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Two-Sided Market Personas (Inspired by Etsy/Airbnb About Pages) -->
        <div class="row g-4 mb-5 pb-4">
            <div class="col-12 text-center mb-4">
                <span class="text-uppercase tracking-wider text-primary fw-bold small">Built For Our Community</span>
                <h3 class="fw-bold text-dark mt-1">Two Sides of One Powerful Ecosystem</h3>
            </div>
            
            <div class="col-md-6">
                <div class="card persona-card p-5 h-100">
                    <div class="icon-shape" style="background-color: #dbeafe; color: #1e3c72;">🛍️</div>
                    <h4 class="fw-bold text-dark mb-3">For Local Consumers</h4>
                    <p class="text-secondary small lh-lg mb-0">
                        Stop searching aimlessly for a reliable professional or unique local product. Our direct categorization matrix lets you discover highly skilled local professionals, check their services, look through pricing transparency, and support community vendors right down your street.
                    </p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card persona-card p-5 h-100">
                    <div class="icon-shape" style="background-color: #dcfce7; color: #15803d;">💼</div>
                    <h4 class="fw-bold text-dark mb-3">For Kasi Entrepreneurs</h4>
                    <p class="text-secondary small lh-lg mb-0">
                        Take your micro-business or side-hustle to a professional digital level. Launch a catalog storefront instantly, tap into a continuous stream of verified local traffic, build direct customer trust records, and scale your brand footprints without expensive tech costs.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Unified Footer Component -->
    <footer class="bg-white border-top py-4 text-center text-secondary small">
        <div class="container">
            &copy; <?php echo date("Y"); ?> EkasiConnect Platform. Built to Empower Local Township Enterprises.
        </div>
    </footer>

</body>
</html>