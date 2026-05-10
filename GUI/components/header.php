<?php
// Tệp: GUI/components/header.php

// 1. CẤU HÌNH MENU DÙNG CHUNG
$menuItems = [
    ['url' => '../analytics/dashboard.php', 'icon' => 'bi-house-door-fill', 'title' => 'Tổng quan', 'keyword' => 'dashboard'],
        ['url' => '../calendar/index.php', 'icon' => 'bi-calendar3', 'title' => 'Lịch tháng', 'keyword' => 'calendar'],
    ['url' => '../transactions/index.php', 'icon' => 'bi-cash-stack', 'title' => 'Giao dịch', 'keyword' => 'transactions'],
    ['url' => '../budgets/index.php', 'icon' => 'bi-bullseye', 'title' => 'Danh mục và Ngân sách', 'keyword' => 'budgets'],
    ['url' => '../ai/advisor.php', 'icon' => 'bi-robot', 'title' => 'Cố vấn AI', 'keyword' => 'ai'],
];
$currentUri = $_SERVER['REQUEST_URI'];

// 2. LẤY THÔNG TIN AVATAR MỚI NHẤT TỪ DATABASE
$headerUserId = $_SESSION['user_id'] ?? null;
$headerUserName = $_SESSION['user_name'] ?? 'Khách';
// Avatar mặc định nếu chưa có ảnh
$headerAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($headerUserName) . '&background=198754&color=fff';

if ($headerUserId) {
    // Khởi tạo DAL để lấy dữ liệu mới nhất (đảm bảo đồng bộ ngay lập tức)
    $headerUserDAL = new UserDAL();
    $headerUser = $headerUserDAL->getUserById($headerUserId);
    
    if ($headerUser) {
        $headerUserName = $headerUser['full_name'];
        if (!empty($headerUser['avatar_url'])) {
            // Đường dẫn tương đối: Lùi 2 cấp (../../) để từ file trang hiện tại về thư mục gốc chứa assets
            $headerAvatar = '../../' . ltrim($headerUser['avatar_url'], '/');
        }
    }
}
?>

<style>
    /* CSS CHO NÚT THÊM GIAO DỊCH NỔI Ở GÓC DƯỚI (MOBILE) */
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

    /* CSS ĐẢM BẢO AVATAR HEADER LUÔN TRÒN VÀ KHÔNG BỊ MÉO */
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