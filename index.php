<?php
session_start();
require_once 'db_connect.php';

// Fetch active marketplace filters (Search text and Category pills)
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build secure SQL syntax dynamically using prepared statements depending on what the user fills in
if (!empty($search_query) && !empty($category_filter)) {
    // Scenario A: User selected a category AND typed a specific term into the search bar
    $query = "SELECT * FROM products WHERE category = ? AND (title LIKE ? OR description LIKE ?) ORDER BY product_id DESC";
    $stmt = $conn->prepare($query);
    $like_search = "%" . $search_query . "%";
    $stmt->bind_param("sss", $category_filter, $like_search, $like_search);
} else if (!empty($search_query)) {
    // Scenario B: User is searching across all categories using the text input field
    $query = "SELECT * FROM products WHERE title LIKE ? OR description LIKE ? ORDER BY product_id DESC";
    $stmt = $conn->prepare($query);
    $like_search = "%" . $search_query . "%";
    $stmt->bind_param("ss", $like_search, $like_search);
} else if (!empty($category_filter)) {
    // Scenario C: User is only filtering by clicking a category pill button
    $query = "SELECT * FROM products WHERE category = ? ORDER BY product_id DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category_filter);
} else {
    // Scenario D: Default state - show all items in the grid
    $query = "SELECT * FROM products ORDER BY product_id DESC";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekasi Connect - Community Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background: #1e3c72; }
        .product-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-5px); }
        .img-container { height: 200px; background-color: #eaeff5; display: flex; align-items: center; justify-content: center; overflow: hidden; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
        .price-tag { font-size: 1.25rem; font-weight: 700; color: #1e3c72; }
        
        /* Premium Search Input Custom Styles */
        .search-wrapper { max-width: 600px; margin: 0 auto; }
        .search-input-custom { border-radius: 30px 0 0 30px !important; border: 1px solid #ced4da; padding-left: 20px; }
        .search-input-custom:focus { box-shadow: none; border-color: #1e3c72; }
        .search-btn-custom { border-radius: 0 30px 30px 0 !important; background-color: #1e3c72; color: #ffffff; padding-left: 25px; padding-right: 25px; }
        .search-btn-custom:hover { background-color: #152b52; color: #ffffff; }
        
        /* Premium Register Button Styling */
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
                <a href="about.php" class="btn btn-link text-white text-decoration-none small me-2 px-2 fw-medium" style="font-size: 0.9rem; opacity: 0.9;">About Us</a>

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

    <div class="container my-5">
        
        <div class="search-wrapper mb-5">
            <form action="index.php" method="GET">
                <?php if (!empty($category_filter)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                <?php endif; ?>
                
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <input type="text" name="search" class="form-control form-control-lg search-input-custom" 
                           placeholder="Search for items, clothing, or local services..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button class="btn search-btn-custom fw-medium" type="submit">Search</button>
                </div>
            </form>
        </div>
        
        <div class="d-flex justify-content-center gap-2 mb-5 flex-wrap">
            <a href="index.php<?php echo !empty($search_query) ? '?search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo empty($category_filter) ? 'btn-primary' : 'btn-outline-secondary'; ?>">All Items</a>
            <a href="index.php?category=Electronics<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo $category_filter === 'Electronics' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Electronics</a>
            <a href="index.php?category=Services<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo $category_filter === 'Services' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Local Services</a>
            <a href="index.php?category=Food<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo $category_filter === 'Food' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Food &amp; Catering</a>
            <a href="index.php?category=Clothing<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo $category_filter === 'Clothing' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Clothing</a>
            <a href="index.php?category=General<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="btn btn-sm rounded-pill px-4 <?php echo $category_filter === 'General' ? 'btn-primary' : 'btn-outline-secondary'; ?>">General Assets</a>
        </div>

        <?php if (!empty($search_query) || !empty($category_filter)): ?>
            <div class="text-center mb-4">
                <p class="text-muted small">
                    Showing results for: 
                    <?php if(!empty($search_query)) echo 'Keyword "<strong>'.htmlspecialchars($search_query).'</strong>"'; ?>
                    <?php if(!empty($search_query) && !empty($category_filter)) echo ' in '; ?>
                    <?php if(!empty($category_filter)) echo 'Category "<strong>'.htmlspecialchars($category_filter).'</strong>"'; ?>
                    • <a href="index.php" class="text-decoration-none">Clear Filters</a>
                </p>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()):
                    $image_filename = trim($row['image_path']);
                    $image_src = "uploads/" . $image_filename;
                    $has_valid_image = (!empty($image_filename) && $image_filename !== 'default.jpg' && $image_filename !== '0');
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100 bg-white">
                            <div class="img-container">
                                <?php if ($has_valid_image): ?>
                                    <img src="<?php echo $image_src; ?>" alt="Product Item">
                                <?php else: ?>
                                    <span style="font-size: 3rem;">📦</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <span class="badge bg-secondary-subtle text-secondary small align-self-start mb-2"><?php echo htmlspecialchars($row['category']); ?></span>
                                <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="price-tag">R <?php echo number_format($row['price'], 2); ?></span>
                                    <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-medium">View Item</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center my-5 py-5">
                    <h4 class="text-muted">No items matched your marketplace filters.</h4>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill mt-2">Reset Grid</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-white border-top py-4 text-center mt-5 text-secondary small">
        <div class="container">
            &copy; <?php echo date("Y"); ?> EkasiConnect Platform. Built to Empower Local Township Enterprises.
        </div>
    </footer>

    
</body>
</html>