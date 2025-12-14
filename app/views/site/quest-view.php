<?php
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $quest app\models\Quests */
/** @var $stationsCount int */
/** @var $participantsCount int */
/** @var $currentParticipant app\models\QuestParticipants|null */
/** @var $questProgress array|null */
$this->title = 'Questable - ' . Html::encode($quest->name);
?>
<div class="container">
    <!-- Кнопка назад -->
    <div class="back-btn-wrap">
        <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Назад к главной
        </a>
    </div>
    <!-- Превью квеста -->
    <div class="quest-hero">
        <?php if ($quest->cover_image_url): ?>
            <img src="<?= Html::encode($quest->cover_image_url) ?>" alt="<?= Html::encode($quest->name) ?>">
        <?php else: ?>
            <div class="quest-hero-placeholder">
                <i class="fas fa-map-marked-alt"></i>
            </div>
        <?php endif; ?>
        <div class="quest-hero-overlay">
            <h1>
                <?= Html::encode($quest->name) ?>
            </h1>
        </div>
    </div>
    <!-- Статистика -->
    <div class="quest-stats">
        <div class="stat-item">
            <div class="stat-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $stationsCount ?></strong>
                <div>Станций</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $participantsCount ?></strong>
                <div>Участников</div>
            </div>
        </div>
        <?php if ($currentParticipant && $questProgress): ?>
        <div class="stat-item">
            <div class="stat-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $currentParticipant->points ?></strong>
                <div>Ваши очки</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <!-- Описание -->
    <div class="quest-description">
        <h2>Описание квеста</h2>
        <?php if ($quest->description): ?>
            <div>
                <?= nl2br(Html::encode($quest->description)) ?>
            </div>
        <?php else: ?>
            <p>Описание отсутствует</p>
        <?php endif; ?>
    </div>
    <!-- Прогресс (если пользователь участвует) -->
    <?php if ($currentParticipant && $questProgress): ?>
        <div class="progress-section">
            <h3>Ваш прогресс</h3>
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Прогресс прохождения</span>
                    <strong><?= $questProgress['completed_stations'] ?>/<?= $questProgress['total_stations'] ?> станций</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success"
                         style="width: <?= $questProgress['progress_percentage'] ?>%"
                         role="progressbar">
                        <?= $questProgress['progress_percentage'] ?>%
                    </div>
                </div>
            </div>
            <?php if ($questProgress['is_completed']): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-trophy me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Поздравляем!</strong> Вы завершили этот квест.
                    </div>
                    <a href="<?= Url::to(['game/completion', 'quest_id' => $quest->id]) ?>"
                       class="btn btn-light btn-sm">
                        <i class="fas fa-star me-1"></i> Страница завершения
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <!-- Кнопки действий -->
    <div class="action-buttons">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= Url::to(['auth/login', 'returnUrl' => Url::to(['site/view', 'id' => $quest->id], true)]) ?>"
               class="btn btn-primary btn-large">
                <i class="fas fa-sign-in-alt"></i> Войти для начала квеста
            </a>
        <?php elseif ($currentParticipant): ?>
            <?php if ($currentParticipant->isBanned()): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-ban"></i> Вы дисквалифицированы из этого квеста.
                </div>
            <?php else: ?>
                <?php if ($questProgress && $questProgress['next_station']): ?>
                    <a href="<?= Url::to(['game/visit', 'qr' => $questProgress['next_station']->qr_identifier]) ?>"
                       class="btn btn-primary btn-large">
                        <i class="fas fa-play"></i> Продолжить квест
                    </a>
                <?php elseif ($questProgress && $questProgress['is_completed']): ?>
                    <a href="<?= Url::to(['game/completion', 'quest_id' => $quest->id]) ?>"
                       class="btn btn-success btn-large">
                        <i class="fas fa-trophy"></i> Страница завершения
                    </a>
                <?php else: ?>
                    <a href="<?= Url::to(['game/progress', 'quest_id' => $quest->id]) ?>"
                       class="btn btn-success btn-large">
                        <i class="fas fa-trophy"></i> Посмотреть результаты
                    </a>
                <?php endif; ?>
                <a href="<?= Url::to(['game/progress', 'quest_id' => $quest->id]) ?>"
                   class="btn btn-outline-primary btn-large">
                    <i class="fas fa-chart-line"></i> Мой прогресс
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?= Url::to(['site/start', 'id' => $quest->id]) ?>"
               class="btn btn-primary btn-large">
                <i class="fas fa-play"></i> Начать квест
            </a>
        <?php endif; ?>
    </div>
</div>