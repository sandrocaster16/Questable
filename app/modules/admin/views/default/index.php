<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $totalUsers */
/** @var int $activeUsers */
/** @var int $bannedUsers */
/** @var int $adminUsers */
/** @var int $volunteerUsers */
/** @var int $totalQuests */
/** @var int $activeQuests */
/** @var int $totalStations */
/** @var int $totalParticipants */
/** @var int $activeParticipants */
/** @var int $bannedParticipants */
/** @var int $totalTeams */
/** @var int $completedQuests */
/** @var array $recentUsers */
/** @var array $recentQuests */
/** @var array $topQuestsByParticipants */

$this->title = 'Административная панель';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-enhancements.css');
?>

<div class="admin-default-index">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-1">
                            <i class="fas fa-tachometer-alt text-primary"></i> <?= Html::encode($this->title) ?>
                        </h1>
                        <p class="text-muted mb-0">Обзор системы и статистика</p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-calendar-alt me-1"></i> <?= date('d.m.Y') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика пользователей -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">
                    <i class="fas fa-users"></i> Пользователи
                    <a href="<?= Url::to(['users/index']) ?>" class="btn btn-sm btn-outline-primary float-end">
                        Управление <i class="fas fa-arrow-right"></i>
                    </a>
                </h3>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Всего пользователей</h6>
                                <h2 class="mb-0"><?= $totalUsers ?></h2>
                            </div>
                            <div class="text-primary" style="font-size: 2.5rem;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Активных</h6>
                                <h2 class="mb-0"><?= $activeUsers ?></h2>
                            </div>
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Забанено</h6>
                                <h2 class="mb-0"><?= $bannedUsers ?></h2>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-user-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Администраторов</h6>
                                <h2 class="mb-0"><?= $adminUsers ?></h2>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($volunteerUsers > 0): ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Волонтеров</h6>
                                <h2 class="mb-0"><?= $volunteerUsers ?></h2>
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Статистика квестов -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">
                    <i class="fas fa-map-marked-alt"></i> Квесты
                    <a href="<?= Url::to(['quests/index']) ?>" class="btn btn-sm btn-outline-primary float-end">
                        Управление <i class="fas fa-arrow-right"></i>
                    </a>
                </h3>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Всего квестов</h6>
                                <h2 class="mb-0"><?= $totalQuests ?></h2>
                            </div>
                            <div class="text-primary" style="font-size: 2.5rem;">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Активных квестов</h6>
                                <h2 class="mb-0"><?= $activeQuests ?></h2>
                            </div>
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Всего станций</h6>
                                <h2 class="mb-0"><?= $totalStations ?></h2>
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-map-pin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика участников -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">
                    <i class="fas fa-user-friends"></i> Участники квестов
                </h3>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Всего участников</h6>
                                <h2 class="mb-0"><?= $totalParticipants ?></h2>
                            </div>
                            <div class="text-primary" style="font-size: 2.5rem;">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Активных</h6>
                                <h2 class="mb-0"><?= $activeParticipants ?></h2>
                            </div>
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Забанено</h6>
                                <h2 class="mb-0"><?= $bannedParticipants ?></h2>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Команд</h6>
                                <h2 class="mb-0"><?= $totalTeams ?></h2>
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые ссылки -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3"><i class="fas fa-link"></i> Быстрые ссылки</h3>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="<?= Url::to(['users/index']) ?>" class="card text-decoration-none border-primary h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5>Управление пользователями</h5>
                        <p class="text-muted mb-0">Просмотр и редактирование пользователей</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="<?= Url::to(['quests/index']) ?>" class="card text-decoration-none border-success h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marked-alt fa-3x text-success mb-3"></i>
                        <h5>Управление квестами</h5>
                        <p class="text-muted mb-0">Создание и редактирование квестов</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="<?= Url::to(['quests-stations/index']) ?>" class="card text-decoration-none border-info h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-map-pin fa-3x text-info mb-3"></i>
                        <h5>Станции квестов</h5>
                        <p class="text-muted mb-0">Управление станциями</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="<?= Url::to(['quests-users/index']) ?>" class="card text-decoration-none border-warning h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-friends fa-3x text-warning mb-3"></i>
                        <h5>Участники квестов</h5>
                        <p class="text-muted mb-0">Просмотр участников</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Последние пользователи -->
        <?php if (!empty($recentUsers)): ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-plus"></i> Последние пользователи
                            <a href="<?= Url::to(['users/index']) ?>" class="btn btn-sm btn-light float-end">
                                Все <i class="fas fa-arrow-right"></i>
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentUsers as $user): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= Html::encode($user->username) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d.m.Y H:i', strtotime($user->created_at)) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-<?= $user->role === 'admin' || $user->role === 'root' ? 'warning' : ($user->role === 'volunteer' ? 'info' : 'secondary') ?>">
                                            <?= Html::encode($user->displayRole()) ?>
                                        </span>
                                        <a href="<?= Url::to(['users/view', 'id' => $user->id]) ?>" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Последние квесты -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt"></i> Последние квесты
                            <a href="<?= Url::to(['quests/index']) ?>" class="btn btn-sm btn-light float-end">
                                Все <i class="fas fa-arrow-right"></i>
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentQuests as $quest): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= Html::encode($quest->name) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d.m.Y H:i', strtotime($quest->created_at)) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?= Url::to(['quests/view', 'id' => $quest->id]) ?>" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Топ квесты по участникам -->
        <?php if (!empty($topQuestsByParticipants)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy"></i> Топ квесты по количеству участников
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Место</th>
                                        <th>Название квеста</th>
                                        <th>Участников</th>
                                        <th>Дата создания</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topQuestsByParticipants as $index => $quest): ?>
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
                                                <strong><?= Html::encode($quest->name) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= isset($quest->participants_count) ? $quest->participants_count : 0 ?></span>
                                            </td>
                                            <td>
                                                <?= date('d.m.Y H:i', strtotime($quest->created_at)) ?>
                                            </td>
                                            <td>
                                                <a href="<?= Url::to(['quests/view', 'id' => $quest->id]) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Просмотр
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .admin-default-index .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .admin-default-index .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .admin-default-index .card-body h2 {
        font-weight: bold;
    }
</style>

