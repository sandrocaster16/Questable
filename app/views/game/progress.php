<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $progress array */

$this->title = 'Прогресс по квесту: ' . Html::encode($progress['quest']->name);
?>

<div class="container py-4" style="max-width: 800px;">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
        <div class="card-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold"><?= Html::encode($progress['quest']->name) ?></h4>
                    <small class="opacity-75">Прогресс прохождения</small>
                </div>
                <div class="text-end">
                    <div class="fs-2 fw-bold"><?= $progress['progress_percentage'] ?>%</div>
                    <small class="opacity-75">Завершено</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="progress mb-4" style="height: 20px; border-radius: 10px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: <?= $progress['progress_percentage'] ?>%" 
                     aria-valuenow="<?= $progress['progress_percentage'] ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 text-center p-3 bg-light rounded">
                    <div class="fs-3 fw-bold text-primary"><?= $progress['total_stations'] ?></div>
                    <small class="text-muted">Всего станций</small>
                </div>
                <div class="col-md-4 text-center p-3 bg-light rounded">
                    <div class="fs-3 fw-bold text-success"><?= $progress['completed_stations'] ?></div>
                    <small class="text-muted">Пройдено</small>
                </div>
                <div class="col-md-4 text-center p-3 bg-light rounded">
                    <div class="fs-3 fw-bold text-warning"><?= $progress['pending_stations'] ?></div>
                    <small class="text-muted">Осталось</small>
                </div>
            </div>

            <div class="mb-3">
                <h5 class="fw-bold mb-3">Статистика</h5>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                    <span><i class="fas fa-coins text-warning me-2"></i> Очки</span>
                    <strong><?= $progress['participant']->points ?></strong>
                </div>
                <?php if ($progress['participant']->team): ?>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                    <span><i class="fas fa-users text-info me-2"></i> Команда</span>
                    <strong><?= Html::encode($progress['participant']->team->name) ?></strong>
                </div>
                <?php endif; ?>
                <?php if ($progress['is_completed']): ?>
                <div class="alert alert-success d-flex align-items-center mt-3">
                    <i class="fas fa-trophy fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Квест завершен!</strong>
                        <div class="small">Поздравляем с прохождением квеста!</div>
                    </div>
                    <a href="<?= Url::to(['game/completion', 'quest_id' => $progress['quest']->id]) ?>" 
                       class="btn btn-light btn-sm">
                        <i class="fas fa-star me-1"></i> Страница завершения
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Станции</h5>
        </div>
        <div class="card-body p-0">
            <?php foreach ($progress['stations_progress'] as $index => $stationProgress): ?>
                <?php $station = $stationProgress['station']; ?>
                <?php $isCompleted = $stationProgress['is_completed']; ?>
                <div class="p-3 border-bottom <?= $isCompleted ? 'bg-light' : '' ?>">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <?php if ($isCompleted): ?>
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            <?php else: ?>
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <?= $index + 1 ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?= Html::encode($station->name) ?></h6>
                            <small class="text-muted">
                                <?php if ($isCompleted && $stationProgress['completed_at']): ?>
                                    <i class="fas fa-check-circle text-success"></i> 
                                    Завершено <?= date('d.m.Y H:i', strtotime($stationProgress['completed_at'])) ?>
                                <?php else: ?>
                                    <i class="fas fa-clock text-warning"></i> Не пройдено
                                <?php endif; ?>
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-<?= $isCompleted ? 'success' : 'secondary' ?>">
                                <?= $station->displayType() ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-primary" style="min-width: 150px;">
            <i class="fas fa-arrow-left me-2"></i> На главную
        </a>
    </div>
</div>

