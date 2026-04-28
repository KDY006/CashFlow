<?php
// Tệp: GUI/pages/ai/advisor.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4">
        <div class="row mb-4 align-items-center g-3">
            <div class="col-12 col-md-8">
                <h3 class="fw-bold text-dark mb-1"><i class="bi bi-robot me-2 text-primary"></i>Trợ lý Tài chính AI</h3>
                <p class="text-muted mb-0">Phân tích dữ liệu giao dịch để đưa ra cảnh báo và lời khuyên tối ưu dòng tiền.</p>
            </div>
            <div class="col-12 col-md-4 text-md-end">
                <form action="../../controllers/AiController.php" method="POST">
                    <input type="hidden" name="action" value="analyze">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-magic me-1"></i> Bắt đầu Phân tích
                    </button>
                </form>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2 border-0 shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success py-2 border-0 shadow-sm rounded-3"><i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <?php if (empty($insights)): ?>
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                        <i class="bi bi-cpu text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="fw-bold text-dark">Chưa có dữ liệu phân tích nào</h5>
                        <p class="text-muted mb-0">Hãy bấm nút "Bắt đầu Phân tích" để AI quét dữ liệu của bạn.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($insights as $insight): ?>
                        <?php 
                            $bgClass = 'bg-white';
                            $icon = 'bi-lightbulb-fill text-warning';
                            $title = 'Lời khuyên';
                            $borderClass = 'border-warning';

                            if ($insight['type'] === 'anomaly') {
                                $icon = 'bi-exclamation-octagon-fill text-danger';
                                $title = 'Phát hiện Bất thường';
                                $borderClass = 'border-danger';
                            } elseif ($insight['type'] === 'forecast') {
                                $icon = 'bi-graph-up-arrow text-info';
                                $title = 'Dự báo Tương lai';
                                $borderClass = 'border-info';
                            }

                            $opacity = $insight['is_read'] ? 'opacity-50' : '';
                        ?>
                        
                        <div class="card mb-3 border-0 border-start border-4 <?= $borderClass ?> rounded-4 shadow-sm <?= $opacity ?>">
                            <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h6 class="fw-bold mb-2"><i class="bi <?= $icon ?> fs-5 me-2"></i><?= $title ?></h6>
                                    <p class="mb-2 text-dark" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($insight['content'])) ?></p>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i • d/m/Y', strtotime($insight['created_at'])) ?></small>
                                </div>
                                
                                <?php if (!$insight['is_read']): ?>
                                <form action="../../controllers/AiController.php" method="POST" class="m-0 flex-shrink-0">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary hover-primary">Đã hiểu</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>