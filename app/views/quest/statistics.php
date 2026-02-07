<?php
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $quest app\models\Quests */
/** @var $statistics array */
$this->title = 'Questable - Статистика ' . Html::encode($quest->name);
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-chart-bar text-primary me-2"></i><?= Html::encode($quest->name) ?>
        </h2>
        <a href="<?= Url::to(['quest/update', 'id' => $quest->id]) ?>" class="btn btn-outline-primary" >
            <i class="fas fa-edit me-2"></i> Редактировать
        </a>
    </div>
    <div class="alert alert-info border-start border-info border-4 mb-4">
        <i class="fas fa-chart-line"></i>
        <strong>Статистика квеста:</strong> Здесь представлена подробная информация о прохождении квеста участниками.
    </div>
    <!-- Общая статистика -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-4" >
                <div class="fs-2 fw-bold text-primary"><?= $statistics['total_stations'] ?></div>
                <div class="text-muted">Станций</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-4" >
                <div class="fs-2 fw-bold text-info"><?= $statistics['total_participants'] ?></div>
                <div class="text-muted">Участников</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-4" >
                <div class="fs-2 fw-bold text-success"><?= $statistics['completed_quest'] ?></div>
                <div class="text-muted">Завершили</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-4" >
                <div class="fs-2 fw-bold text-warning"><?= $statistics['completion_rate'] ?>%</div>
                <div class="text-muted">Процент завершения</div>
            </div>
        </div>
    </div>
    <!-- Статистика по станциям -->
    <div class="card shadow-sm border-0 mb-4" >
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Статистика по станциям</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Станция</th>
                            <th>Тип</th>
                            <th>Пройдено участниками</th>
                            <th>Процент прохождения</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistics['stations_statistics'] as $stationStat): ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($stationStat['station']->name) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $stationStat['station']->displayType() ?></span>
                                </td>
                                <td>
                                    <strong><?= $stationStat['completed_by'] ?></strong> / <?= $statistics['total_participants'] ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2">
                                            <div class="progress-bar"
                                                 style="width: <?= $stationStat['completion_rate'] ?>%"
                                                 role="progressbar">
                                            </div>
                                        </div>
                                        <span class="text-muted small"><?= $stationStat['completion_rate'] ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Топ участников -->
    <?php $topParticipants = $quest->getTopParticipants(10); ?>
    <?php if (!empty($topParticipants)): ?>
    <div class="card shadow-sm border-0 mb-4" >
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Топ участников</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Место</th>
                            <th>Участник</th>
                            <th>Очки</th>
                            <th>Прогресс</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topParticipants as $index => $item): ?>
                            <?php $participant = $item['participant']; ?>
                            <?php $progress = $item['progress']; ?>
                            <tr>
                                <td>
                                    <?php if ($index === 0): ?>
                                        <span class="badge bg-warning text-dark">🥇</span>
                                    <?php elseif ($index === 1): ?>
                                        <span class="badge bg-secondary">🥈</span>
                                    <?php elseif ($index === 2): ?>
                                        <span class="badge bg-danger">🥉</span>
                                    <?php else: ?>
                                        <strong>#<?= $index + 1 ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= Html::encode($participant->user->username) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= $participant->points ?></span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success"
                                             style="width: <?= $progress['progress_percentage'] ?>%"
                                             role="progressbar">
                                            <?= $progress['completed_stations'] ?>/<?= $progress['total_stations'] ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($progress['is_completed']): ?>
                                        <span class="badge bg-success">Завершен</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">В процессе</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Топ команд -->
    <?php $topTeams = $quest->getTopTeams(10); ?>
    <?php if (!empty($topTeams)): ?>
    <div class="card shadow-sm border-0 mb-4" >
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Топ команд</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Место</th>
                            <th>Команда</th>
                            <th>Участников</th>
                            <th>Общие очки</th>
                            <th>Прогресс</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topTeams as $index => $teamProgress): ?>
                            <tr>
                                <td>
                                    <?php if ($index === 0): ?>
                                        <span class="badge bg-warning text-dark">🥇</span>
                                    <?php elseif ($index === 1): ?>
                                        <span class="badge bg-secondary">🥈</span>
                                    <?php elseif ($index === 2): ?>
                                        <span class="badge bg-danger">🥉</span>
                                    <?php else: ?>
                                        <strong>#<?= $index + 1 ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= Html::encode($teamProgress['team']->name) ?></strong>
                                </td>
                                <td><?= count($teamProgress['members']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $teamProgress['total_points'] ?></span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success"
                                             style="width: <?= $teamProgress['progress_percentage'] ?>%"
                                             role="progressbar">
                                            <?= $teamProgress['fully_completed_stations'] ?>/<?= $teamProgress['total_stations'] ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($teamProgress['is_completed']): ?>
                                        <span class="badge bg-success">Завершен</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">В процессе</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="text-center mt-4">
        <a href="<?= Url::to(['quest/index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Назад к списку квестов
        </a>
    </div>
</div>