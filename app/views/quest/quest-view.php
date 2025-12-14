<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $stationsCount int */
/** @var $participantsCount int */
/** @var $currentParticipant app\models\QuestParticipants|null */
/** @var $questProgress array|null */

$this->title = Html::encode($quest->name);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?> - Questable</title>
    <link rel="stylesheet" href="../app/web/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .quest-hero {
            position: relative;
            height: 400px;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 30px;
        }
        .quest-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .quest-hero-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 30px;
            color: white;
        }
        .quest-stats {
            display: flex;
            gap: 30px;
            margin: 20px 0;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        .stat-item:hover {
            border-color: #007bff;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }
        .stat-icon {
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .quest-description {
            background: var(--bg-surface, #fff);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border, #ddd);
            margin-bottom: 30px;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }
        .btn-large {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            min-width: 180px;
            flex: 0 1 auto;
        }
        .btn-large i {
            margin-right: 6px;
        }
        .progress-section {
            background: var(--bg-surface, #fff);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border, #ddd);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <!-- Кнопка назад -->
    <div style="margin-bottom: 20px;">
        <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Назад к главной
        </a>
    </div>

    <!-- Превью квеста -->
    <div class="quest-hero">
        <?php if ($quest->cover_image_url): ?>
            <img src="<?= Html::encode($quest->cover_image_url) ?>" alt="<?= Html::encode($quest->name) ?>">
        <?php else: ?>
            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-map-marked-alt" style="font-size: 100px; color: white; opacity: 0.5;"></i>
            </div>
        <?php endif; ?>
        <div class="quest-hero-overlay">
            <h1 style="font-size: 36px; font-weight: bold; margin: 0 0 10px 0;">
                <?= Html::encode($quest->name) ?>
            </h1>
        </div>
    </div>

    <!-- Статистика -->
    <div class="quest-stats">
        <div class="stat-item">
            <div class="stat-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $stationsCount ?></strong>
                <div style="font-size: 14px; color: #666;">Станций</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $participantsCount ?></strong>
                <div style="font-size: 14px; color: #666;">Участников</div>
            </div>
        </div>
        <?php if ($currentParticipant && $questProgress): ?>
        <div class="stat-item">
            <div class="stat-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <strong class="fs-4"><?= $currentParticipant->points ?></strong>
                <div style="font-size: 14px; color: #666;">Ваши очки</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Описание -->
    <div class="quest-description">
        <h2 style="margin-bottom: 20px; font-size: 24px;">Описание квеста</h2>
        <?php if ($quest->description): ?>
            <div style="line-height: 1.8; font-size: 16px; color: #333;">
                <?= nl2br(Html::encode($quest->description)) ?>
            </div>
        <?php else: ?>
            <p style="color: #999; font-style: italic;">Описание отсутствует</p>
        <?php endif; ?>
    </div>

    <!-- Прогресс (если пользователь участвует) -->
    <?php if ($currentParticipant && $questProgress): ?>
        <div class="progress-section">
            <h3 style="margin-bottom: 20px;">Ваш прогресс</h3>
            <div style="margin-bottom: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Прогресс прохождения</span>
                    <strong><?= $questProgress['completed_stations'] ?>/<?= $questProgress['total_stations'] ?> станций</strong>
                </div>
                <div class="progress" style="height: 25px; border-radius: 12px;">
                    <div class="progress-bar bg-success" 
                         style="width: <?= $questProgress['progress_percentage'] ?>%" 
                         role="progressbar">
                        <?= $questProgress['progress_percentage'] ?>%
                    </div>
                </div>
            </div>
            <?php if ($questProgress['is_completed']): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-trophy fa-2x me-3"></i>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

