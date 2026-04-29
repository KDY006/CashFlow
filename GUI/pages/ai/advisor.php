<?php
// Tệp: GUI/pages/ai/advisor.php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../autoload.php';

$aiBus = new AiAdvisorBUS();
$insights = $aiBus->getUserInsights($_SESSION['user_id']);

// Phân nhóm dữ liệu theo loại
$anomalies = [];
$advices = [];
$forecasts = [];

foreach ($insights as $insight) {
    if ($insight['type'] === 'anomaly') {
        $anomalies[] = $insight;
    } elseif ($insight['type'] === 'advice') {
        $advices[] = $insight;
    } elseif ($insight['type'] === 'forecast') {
        $forecasts[] = $insight;
    }
}
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
    <style>
        /* Tùy chỉnh để nút bấm bảng thả trông giống một tấm thẻ (card) hơn */
        .accordion-button:not(.collapsed) { background-color: #fff; color: #000; box-shadow: none; border-bottom: 1px solid #dee2e6; }
        .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
        .insight-card { transition: all 0.2s ease; }
        .insight-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-light">

    <?php require_once __DIR__ . '/../../components/header.php'; ?>

    <main class="container py-4 mb-5">
        <div class="row mb-4 align-items-center g-3">
            <div class="col-12 col-md-8">
                <h3 class="fw-bold text-dark mb-1"><i class="bi bi-robot me-2 text-primary"></i>Trợ lý Tài chính AI</h3>
                <p class="text-muted mb-0">Phân tích dữ liệu để đưa ra cảnh báo và lời khuyên tối ưu dòng tiền.</p>
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

    <!-- THÔNG BÁO LỖI TỔNG QUÁT (CÓ NÚT TẮT) -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show py-3 border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                <div><i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- THÔNG BÁO THÀNH CÔNG (CÓ NÚT TẮT) -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show py-3 border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                <div><i class="bi bi-check-circle-fill me-2 fs-5"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($insights)): ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <i class="bi bi-cpu text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="fw-bold text-dark">Chưa có dữ liệu phân tích nào</h5>
                <p class="text-muted mb-0">Hãy bấm nút "Bắt đầu Phân tích" để AI quét dữ liệu của bạn.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                
                <!-- ============================================== -->
                <!-- CỘT 1: CẢNH BÁO BẤT THƯỜNG -->
                <!-- ============================================== -->
                <div class="col-12 col-lg-4">
                    <?php if (empty($anomalies)): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-exclamation-octagon-fill"></i></div>
                            <h5 class="fw-bold text-dark mb-0">Cảnh báo</h5>
                        </div>
                        <p class="text-muted small fst-italic">Không có khoản chi tiêu bất thường nào!</p>
                    <?php else: ?>
                        <div class="accordion shadow-sm rounded-4 border-0" id="accAnomaly">
                            <div class="accordion-item border-0 rounded-4">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colAnomaly">
                                        <div class="d-flex flex-column w-100 me-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                                <h6 class="fw-bold text-dark mb-0">Cảnh báo</h6>
                                                <span class="badge bg-danger ms-auto rounded-pill"><?= count($anomalies) ?></span>
                                            </div>
                                            <!-- Hiển thị trích đoạn lời khuyên mới nhất -->
                                            <div class="mt-2 text-muted small text-truncate" style="max-width: 95%;">
                                                <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars(mb_substr($anomalies[0]['content'], 0, 60, 'UTF-8')) ?>...
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="colAnomaly" class="accordion-collapse collapse" data-bs-parent="#accAnomaly">
                                    <div class="accordion-body bg-light rounded-bottom-4 p-3">
                                        <?php foreach ($anomalies as $insight): ?>
                                            <?php $borderColor = $insight['is_read'] ? 'border-secondary' : 'border-danger'; ?>
                                            <div class="card insight-card mb-3 border-0 border-start border-4 <?= $borderColor ?> shadow-sm">
                                                <div class="card-body p-3">
                                                    <?php if (!$insight['is_read']): ?><span class="badge bg-danger mb-2">Mới</span><?php endif; ?>
                                                    <!-- Hiển thị Full nội dung -->
                                                    <p class="mb-2 text-dark small" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($insight['content'])) ?></p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
                                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?= date('H:i d/m', strtotime($insight['created_at'])) ?></small>
                                                        <div class="d-flex gap-1 align-items-center">
                                                            <?php if (!$insight['is_read']): ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0">
                                                                <input type="hidden" name="action" value="mark_read">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 py-0 fw-semibold text-secondary" style="font-size: 0.7rem;">Đã hiểu</button>
                                                            </form>
                                                            <?php endif; ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0" onsubmit="return confirm('Xóa thông báo này?');">
                                                                <input type="hidden" name="action" value="delete_insight">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm text-danger border-0 p-1"><i class="bi bi-trash3"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ============================================== -->
                <!-- CỘT 2: LỜI KHUYÊN TỐI ƯU -->
                <!-- ============================================== -->
                <div class="col-12 col-lg-4">
                    <?php if (empty($advices)): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-lightbulb-fill"></i></div>
                            <h5 class="fw-bold text-dark mb-0">Lời khuyên</h5>
                        </div>
                        <p class="text-muted small fst-italic">Chưa có lời khuyên nào được đưa ra.</p>
                    <?php else: ?>
                        <div class="accordion shadow-sm rounded-4 border-0" id="accAdvice">
                            <div class="accordion-item border-0 rounded-4">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colAdvice">
                                        <div class="d-flex flex-column w-100 me-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-lightbulb-fill"></i></div>
                                                <h6 class="fw-bold text-dark mb-0">Lời khuyên</h6>
                                                <span class="badge bg-warning text-dark ms-auto rounded-pill"><?= count($advices) ?></span>
                                            </div>
                                            <div class="mt-2 text-muted small text-truncate" style="max-width: 95%;">
                                                <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars(mb_substr($advices[0]['content'], 0, 60, 'UTF-8')) ?>...
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="colAdvice" class="accordion-collapse collapse" data-bs-parent="#accAdvice">
                                    <div class="accordion-body bg-light rounded-bottom-4 p-3">
                                        <?php foreach ($advices as $insight): ?>
                                            <?php $borderColor = $insight['is_read'] ? 'border-secondary' : 'border-warning'; ?>
                                            <div class="card insight-card mb-3 border-0 border-start border-4 <?= $borderColor ?> shadow-sm">
                                                <div class="card-body p-3">
                                                    <?php if (!$insight['is_read']): ?><span class="badge bg-warning text-dark mb-2">Mới</span><?php endif; ?>
                                                    <p class="mb-2 text-dark small" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($insight['content'])) ?></p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
                                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?= date('H:i d/m', strtotime($insight['created_at'])) ?></small>
                                                        <div class="d-flex gap-1 align-items-center">
                                                            <?php if (!$insight['is_read']): ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0">
                                                                <input type="hidden" name="action" value="mark_read">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 py-0 fw-semibold text-secondary" style="font-size: 0.7rem;">Đã hiểu</button>
                                                            </form>
                                                            <?php endif; ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0" onsubmit="return confirm('Xóa thông báo này?');">
                                                                <input type="hidden" name="action" value="delete_insight">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm text-danger border-0 p-1"><i class="bi bi-trash3"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ============================================== -->
                <!-- CỘT 3: DỰ BÁO TƯƠNG LAI -->
                <!-- ============================================== -->
                <div class="col-12 col-lg-4">
                    <?php if (empty($forecasts)): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-graph-up-arrow"></i></div>
                            <h5 class="fw-bold text-dark mb-0">Dự báo</h5>
                        </div>
                        <p class="text-muted small fst-italic">Cần thêm dữ liệu giao dịch để dự báo.</p>
                    <?php else: ?>
                        <div class="accordion shadow-sm rounded-4 border-0" id="accForecast">
                            <div class="accordion-item border-0 rounded-4">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colForecast">
                                        <div class="d-flex flex-column w-100 me-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 text-info rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-graph-up-arrow"></i></div>
                                                <h6 class="fw-bold text-dark mb-0">Dự báo</h6>
                                                <span class="badge bg-info ms-auto rounded-pill"><?= count($forecasts) ?></span>
                                            </div>
                                            <div class="mt-2 text-muted small text-truncate" style="max-width: 95%;">
                                                <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars(mb_substr($forecasts[0]['content'], 0, 60, 'UTF-8')) ?>...
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="colForecast" class="accordion-collapse collapse" data-bs-parent="#accForecast">
                                    <div class="accordion-body bg-light rounded-bottom-4 p-3">
                                        <?php foreach ($forecasts as $insight): ?>
                                            <?php $borderColor = $insight['is_read'] ? 'border-secondary' : 'border-info'; ?>
                                            <div class="card insight-card mb-3 border-0 border-start border-4 <?= $borderColor ?> shadow-sm">
                                                <div class="card-body p-3">
                                                    <?php if (!$insight['is_read']): ?><span class="badge bg-info mb-2">Mới</span><?php endif; ?>
                                                    <p class="mb-2 text-dark small" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($insight['content'])) ?></p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
                                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?= date('H:i d/m', strtotime($insight['created_at'])) ?></small>
                                                        <div class="d-flex gap-1 align-items-center">
                                                            <?php if (!$insight['is_read']): ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0">
                                                                <input type="hidden" name="action" value="mark_read">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 py-0 fw-semibold text-secondary" style="font-size: 0.7rem;">Đã hiểu</button>
                                                            </form>
                                                            <?php endif; ?>
                                                            <form action="../../controllers/AiController.php" method="POST" class="m-0" onsubmit="return confirm('Xóa thông báo này?');">
                                                                <input type="hidden" name="action" value="delete_insight">
                                                                <input type="hidden" name="insight_id" value="<?= $insight['id'] ?>">
                                                                <button type="submit" class="btn btn-sm text-danger border-0 p-1"><i class="bi bi-trash3"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../../components/bottom-nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>