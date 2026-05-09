<?php
// Tệp: GUI/components/header.php

// 1. CẤU HÌNH MENU DÙNG CHUNG CHO CẢ DESKTOP VÀ MOBILE
$menuItems = [
    ['url' => '../analytics/dashboard.php', 'icon' => 'bi-house-door-fill', 'title' => 'Tổng quan', 'keyword' => 'dashboard'],
    ['url' => '../calendar/index.php', 'icon' => 'bi-calendar3', 'title' => 'Lịch tháng', 'keyword' => 'calendar'],
    ['url' => '../transactions/index.php', 'icon' => 'bi-cash-stack', 'title' => 'Giao dịch', 'keyword' => 'transactions'],
    ['url' => '../budgets/index.php', 'icon' => 'bi-bullseye', 'title' => 'Danh mục và Ngân sách', 'keyword' => 'budgets'],
    ['url' => '../ai/advisor.php', 'icon' => 'bi-robot', 'title' => 'Cố vấn AI', 'keyword' => 'ai'],
];
$currentUri = $_SERVER['REQUEST_URI'];
?>

<style>
    /* CSS CHO NÚT THÊM GIAO DỊCH NỔI Ở GÓC DƯỚI (MOBILE) */
    .fab-mobile {
        position: fixed;
        bottom: 85px; /* Nằm cách đáy 85px (tức là nổi ngay trên thanh Bottom Nav) */
        right: 20px;  /* Cách lề phải 20px */
        z-index: 1050; /* Đảm bảo luôn nằm trên cùng, không bị che khuất */
    }
    .fab-mobile .btn-float {
        width: 56px; 
        height: 56px; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4); /* Đổ bóng màu xanh lá */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .fab-mobile .btn-float:active {
        transform: scale(0.9); /* Hiệu ứng lún xuống khi bấm */
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.4);
    }
    
    /* CSS Bổ sung cho thanh Bottom Nav nếu thiếu */
    .bottom-nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }
</style>

<nav class="navbar navbar-expand-md sticky-top py-2 px-3 bg-white shadow-sm">
    <div class="container-fluid align-items-center">
        
        <a class="navbar-brand text-success fw-bold d-flex align-items-center gap-2" href="../analytics/dashboard.php">
            <i class="bi bi-wallet2 fs-3"></i><span>CashFlow</span>
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
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle p-1 rounded-pill border" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #f0f2f5;">
                    <img src="https://ui-avatars.com/api/?name=<?= isset($_SESSION['user_name']) ? urlencode($_SESSION['user_name']) : 'User' ?>&background=198754&color=fff" alt="Avatar" width="32" height="32" class="rounded-circle me-2">
                    <span class="fw-semibold me-2 d-none d-sm-inline"><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Khách' ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person-circle me-2"></i>Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger py-2" href="../../controllers/AuthController.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
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