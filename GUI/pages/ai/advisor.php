<?php

require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

$aiBus = new AiAdvisorBUS();

$aiBus = new AiAdvisorBUS();
$insights = $aiBus->getUserInsights($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trợ lý AI - CashFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary">🤖 Trợ lý Tài chính AI</h2>
            <p class="text-muted">Phân tích dữ liệu giao dịch để đưa ra cảnh báo và lời khuyên tối ưu dòng tiền.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <form action="../../controllers/AiController.php" method="POST">
                <input type="hidden" name="action" value="analyze">
                <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                    ✨ Bắt đầu Phân tích
                </button>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger py-2"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success py-2"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <?php if (empty($insights)): ?>
                <div class="card border-0 shadow-sm text-center py-5">
                    <h5 class="text-muted">Chưa có dữ liệu phân tích nào.</h5>
                    <p class="text-muted mb-0">Hãy bấm nút "Bắt đầu Phân tích" để AI quét dữ liệu của bạn.</p>
                </div>
            <?php else: ?>
                <?php foreach ($insights as $insight): ?>
                    <?php 
                        // Cấu hình giao diện tùy theo loại cảnh báo
                        $bgClass = 'bg-white';
                        $icon = '💡';
                        $title = 'Lời khuyên';
                        $borderClass = 'border-success';

                        if ($insight['type'] === 'anomaly') {
                            $icon = '⚠️';
                            $title = 'Phát hiện Bất thường';
                            $borderClass = 'border-danger';
                        } elseif ($insight['type'] === 'forecast') {
                            $icon = '📈';
                            $title = 'Dự báo Tương lai';
                            $borderClass = 'border-info';
                        }

                        // Làm mờ nếu đã đọc
                        $opacity = $insight['is_read'] ? 'opacity-50' : '';
                    ?>
                    
                    <div class="card mb-3 border-0 border-start border-4 <?= $borderClass ?> shadow-sm <?= $opacity ?>">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1"><?= $icon ?> <?= $title ?></h6>
                                <p class="mb-0 text-dark"><?= htmlspecialchars($insight['content']) ?></p>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($insight['created_at'])) ?></small>
                            </div>
                            
                            <?php if (!$insight['is_read']): ?>
                            <form action="../../controllers/AiController.php" method="POST" class="m-0">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Đã hiểu</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>