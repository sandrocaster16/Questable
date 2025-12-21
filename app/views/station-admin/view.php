<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;
/** @var $station app\models\QuestStations */
/** @var $pendingParticipants app\models\StationProgress[] */

$this->title = 'Questable - Администрирование станции: ' . Html::encode($station->name);
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="text-uppercase text-muted mb-1">Администрирование станции</h6>
            <h1 class="h3 mb-0 fw-bold"><?= Html::encode($station->name) ?></h1>
        </div>
        <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> На главную
        </a>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle fs-3 me-3"></i>
            <div><?= Yii::$app->session->getFlash('success') ?></div>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-times-circle fs-3 me-3"></i>
            <div><?= Yii::$app->session->getFlash('error') ?></div>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('warning')): ?>
        <div class="alert alert-warning d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-3 me-3"></i>
            <div><?= Yii::$app->session->getFlash('warning') ?></div>
        </div>
    <?php endif; ?>

    <!-- Информация о станции -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-info-circle text-primary me-2"></i>
                Задание станции
            </h5>
        </div>
        <div class="card-body">
            <div class="prose text-break">
                <?php if (!empty($station->content)): ?>
                    <?= Markdown::process($station->content) ?>
                <?php else: ?>
                    <p class="text-muted">Задание не указано</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Список участников -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-users text-primary me-2"></i>
                Участники, ожидающие проверки
            </h5>
            <span class="badge bg-primary rounded-pill"><?= count($pendingParticipants) ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($pendingParticipants)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h6 class="text-muted">Нет участников, ожидающих проверки</h6>
                    <p class="text-muted small">Все участники уже обработаны</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Участник</th>
                                <th>Очки</th>
                                <th class="text-end">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingParticipants as $progress): ?>
                                <?php $participant = $progress->participant; ?>
                                <?php $user = $participant->user; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($user->avatar_url): ?>
                                                <img src="<?= Html::encode($user->avatar_url) ?>" 
                                                     alt="<?= Html::encode($user->username) ?>" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                                    <?= strtoupper(mb_substr($user->username, 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= Html::encode($user->username) ?></strong>
                                                <?php if ($participant->team): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-users"></i> <?= Html::encode($participant->team->name) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill"><?= $participant->points ?? 0 ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <form method="post" action="<?= Url::to(['station-admin/approve', 'progress_id' => $progress->id]) ?>" 
                                                  style="display: inline-block; margin: 0;"
                                                  onsubmit="return confirm('Зачесть выполнение задания для этого участника?');">
                                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                                                <button type="submit" class="btn btn-success btn-sm" title="Зачесть задание">
                                                    <i class="fas fa-check me-1"></i> Зачесть
                                                </button>
                                            </form>
                                            <form method="post" action="<?= Url::to(['station-admin/reject', 'progress_id' => $progress->id]) ?>" 
                                                  style="display: inline-block; margin: 0;"
                                                  onsubmit="return confirm('Отклонить выполнение задания? Участник сможет попробовать снова.');">
                                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                                                <button type="submit" class="btn btn-danger btn-sm" title="Отклонить задание">
                                                    <i class="fas fa-times me-1"></i> Отклонить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mt-4">
        <button onclick="location.reload()" class="btn btn-outline-primary">
            <i class="fas fa-sync-alt me-2"></i> Обновить список
        </button>
    </div>
</div>
