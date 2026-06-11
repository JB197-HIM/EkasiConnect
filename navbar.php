<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-3 me-2" style="width: 40px; height: 40px;">
                <span class="fw-bold fs-5">EC</span>
            </div>
            <div>
                <span class="fw-bold text-dark d-block mb-0 lh-1 fs-5">Ekasi Connect</span>
                <small class="text-muted d-block" style="font-size: 0.72rem; letter-spacing: 0.3px;">Connecting the Kasi Economy, One Click at a Time</small>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ekasiNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="ekasiNav">
            <div class="navbar-nav ms-auto align-items-center gap-2">
                <a class="nav-link text-secondary me-2" href="index.php">Browse Marketplace</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                        <a class="btn btn-sm btn-outline-purple me-2 fw-bold text-purple border-purple" style="color: #6f42c1; border-color: #6f42c1;" href="admin.php">Admin Console</a>
                    <?php endif; ?>
                    
                    <span class="text-dark fw-semibold me-2">Molo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a class="btn btn-sm btn-outline-danger px-3" href="logout.php">Log Out</a>
                <?php else: ?>
                    <a class="btn btn-sm btn-outline-primary px-3" href="login.php">Login</a>
                    <a class="btn btn-sm btn-primary px-3" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>