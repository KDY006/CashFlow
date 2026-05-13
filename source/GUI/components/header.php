<?php

$menuItems = [
    ['url' => '../analytics/dashboard.php', 'icon' => 'bi-house-door-fill', 'title' => 'Tổng quan', 'keyword' => 'dashboard'],
    ['url' => '../calendar/index.php', 'icon' => 'bi-calendar3', 'title' => 'Lịch tháng', 'keyword' => 'calendar'],
    ['url' => '../transactions/index.php', 'icon' => 'bi-cash-stack', 'title' => 'Giao dịch', 'keyword' => 'transactions'],
    ['url' => '../budgets/index.php', 'icon' => 'bi-bullseye', 'title' => 'Danh mục và Ngân sách', 'keyword' => 'budgets'],
    ['url' => '../ai/advisor.php', 'icon' => 'bi-robot', 'title' => 'Cố vấn AI', 'keyword' => 'ai'],
];
$currentUri = $_SERVER['REQUEST_URI'];

$headerUserId = $_SESSION['user_id'] ?? null;
$headerUserName = $_SESSION['user_name'] ?? 'Khách';
$headerAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($headerUserName) . '&background=198754&color=fff';

if ($headerUserId) {
    $headerUserBUS = new UserBUS();
    $headerUser = $headerUserBUS->getUserById($headerUserId);
    
    if ($headerUser) {
        $headerUserName = $headerUser['full_name'];
        if (!empty($headerUser['avatar_url'])) {
            $headerAvatar = '../../assets/images/avatars/' . ltrim($headerUser['avatar_url'], '/');
        }
    }
}
?>

<?php
require_once __DIR__ . '/../../helpers/CsrfHelper.php';
$csrfToken = CsrfHelper::generateToken();
?>

<meta name="csrf-token" content="<?= $csrfToken ?>">

<script>
    (function() {
        const originalFetch = window.fetch;
        window.fetch = async function(resource, config) {
            if (config && config.method && config.method.toUpperCase() === 'POST') {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                
                if (config.body instanceof FormData) {
                    if (!config.body.has('csrf_token')) {
                        config.body.append('csrf_token', token);
                    }
                } 
                else if (typeof config.body === 'string') {
                    try {
                        let data = JSON.parse(config.body);
                        data.csrf_token = token;
                        config.body = JSON.stringify(data);
                    } catch (e) {
                        if (config.body.indexOf('=') !== -1) {
                            config.body += (config.body.length > 0 ? '&' : '') + 'csrf_token=' + encodeURIComponent(token);
                        }
                    }
                }
            }
            return originalFetch(resource, config);
        };
    })();
</script>

<style>
    .fab-mobile {
        position: fixed;
        bottom: 85px; 
        right: 20px;  
        z-index: 1050; 
    }
    .fab-mobile .btn-float {
        width: 56px; 
        height: 56px; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .fab-mobile .btn-float:active {
        transform: scale(0.9);
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.4);
    }
    
    .bottom-nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .header-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #dee2e6;
        background-color: #fff;
    }
</style>

<nav class="navbar navbar-expand-md sticky-top py-2 px-3 bg-white shadow-sm">
    <div class="container-fluid align-items-center">
        
        <link rel="icon" href="../../assets/images/logo/logo.png" type="image/png">

        <a class="navbar-brand text-success fw-bold d-flex align-items-center gap-2" href="../analytics/dashboard.php">
            <img src="../../assets/images/logo/logo.png" alt="CashFlow Logo" style="width: 35px; height: 35px; object-fit: contain;">
            <span>CashFlow</span>
        </a>

        <ul class="navbar-nav mx-auto desktop-nav d-none d-md-flex flex-row gap-2">
            <?php foreach ($menuItems as $item): ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUri, $item['keyword']) !== false ? 'active' : '' ?>" href="<?= $item['url'] ?>" title="<?= $item['title'] ?>">
                        <i class="bi <?= $item['icon'] ?> fs-5"></i> <span class="d-none d-lg-inline"><?= $item['title'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="d-flex align-items-center gap-3">
            <a href="javascript:void(0)" onclick="openGlobalAddModal()" class="btn btn-success rounded-pill px-3 shadow-sm d-none d-md-flex align-items-center fw-bold">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-lg-inline ms-1">Thêm giao dịch</span>
            </a>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle p-1 rounded-pill border" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #f8f9fa;">
                    <img src="<?= htmlspecialchars($headerAvatar) ?>" alt="Avatar" class="header-avatar me-2">
                    <span class="fw-semibold me-2 d-none d-sm-inline"><?= htmlspecialchars($headerUserName) ?></span>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="dropdownUser">
                    <li>
                        <a class="dropdown-item py-2 fw-semibold text-secondary" href="../profile/index.php">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Hồ sơ cá nhân
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger py-2 fw-semibold" href="../../controllers/AuthController.php?action=logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<nav class="bottom-nav d-md-none bg-white shadow-lg border-top">
    <?php foreach ($menuItems as $item): ?>
        <a href="<?= $item['url'] ?>" class="bottom-nav-item <?= strpos($currentUri, $item['keyword']) !== false ? 'active' : '' ?>" style="flex: 1; text-align: center;">
            <i class="bi <?= $item['icon'] ?>"></i>
            <span class="d-block" style="font-size: 0.7rem;"><?= $item['title'] ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<div class="fab-mobile d-md-none">
    <a href="javascript:void(0)" onclick="openGlobalAddModal()" class="btn btn-success btn-float">
        <i class="bi bi-plus-lg fs-3 text-white"></i>
    </a>
</div>

<?php require_once __DIR__ . '/global-add-modal.php'; ?>