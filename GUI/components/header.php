<?php
// 1. CẤU HÌNH MENU DÙNG CHUNG CHO CẢ DESKTOP VÀ MOBILE
$menuItems = [
    ['url' => '../analytics/dashboard.php', 'icon' => 'bi-house-door-fill', 'title' => 'Tổng quan', 'keyword' => 'dashboard'],
    ['url' => '../transactions/index.php', 'icon' => 'bi-cash-stack', 'title' => 'Giao dịch', 'keyword' => 'transactions'],
    ['url' => '../calendar/index.php', 'icon' => 'bi-calendar3', 'title' => 'Lịch', 'keyword' => 'calendar'],
    ['url' => '../budgets/index.php', 'icon' => 'bi-bullseye', 'title' => 'Ngân sách', 'keyword' => 'budgets'],
    ['url' => '../ai/advisor.php', 'icon' => 'bi-robot', 'title' => 'Cố vấn AI', 'keyword' => 'ai'],
];
$currentUri = $_SERVER['REQUEST_URI'];
?>

<nav class="navbar navbar-expand-md sticky-top py-2 px-3">
    <div class="container-fluid align-items-center">
        
        <a class="navbar-brand text-success fw-bold d-flex align-items-center gap-2" href="../analytics/dashboard.php">
            <i class="bi bi-wallet2 fs-3"></i><span>CashFlow</span>
        </a>

        <ul class="navbar-nav mx-auto desktop-nav d-flex flex-row">
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

<nav class="bottom-nav d-md-none">
    <?php foreach ($menuItems as $index => $item): ?>
        
        <?php if ($index == 2): ?>
            <div class="fab-container">
                <a href="javascript:void(0)" onclick="openGlobalAddModal()" class="fab-button shadow">
                    <i class="bi bi-plus-lg text-white"></i>
                </a>
            </div>
        <?php endif; ?>

        <a href="<?= $item['url'] ?>" class="bottom-nav-item <?= strpos($currentUri, $item['keyword']) !== false ? 'active' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i>
            <span><?= $item['title'] ?></span>
        </a>
        
    <?php endforeach; ?>
</nav>

<?php require_once __DIR__ . '/global-add-modal.php'; ?>