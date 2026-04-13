<nav class="bottom-nav">
    <a href="../analytics/dashboard.php" class="bottom-nav-item <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-house-door-fill"></i>
        <span>Tổng quan</span>
    </a>
    
    <a href="../transactions/index.php" class="bottom-nav-item <?= strpos($_SERVER['REQUEST_URI'], 'transactions') ? 'active' : '' ?>">
        <i class="bi bi-cash-stack"></i>
        <span>Giao dịch</span>
    </a>
    
    <a href="../budgets/index.php" class="bottom-nav-item <?= strpos($_SERVER['REQUEST_URI'], 'budgets') ? 'active' : '' ?>">
        <i class="bi bi-bullseye"></i>
        <span>Ngân sách</span>
    </a>
    
    <a href="../ai/advisor.php" class="bottom-nav-item <?= strpos($_SERVER['REQUEST_URI'], 'ai') ? 'active' : '' ?>">
        <i class="bi bi-robot"></i>
        <span>Cố vấn AI</span>
    </a>
</nav>