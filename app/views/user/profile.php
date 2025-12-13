<?php
use yii\helpers\Html;

/** @var $completedQuestCount int */
/** @var $createdQuestCount int */
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Questable</title>
</head>
<body>

<div class="container">
    <h2 style="margin-bottom: 20px;">Настройки профиля</h2>

    <div class="profile-wrap">
        <!-- профиль -->
        <div class="profile-box">
            <form action="" method="POST" enctype="multipart/form-data">
                <img src="<?= Html::encode(Yii::$app->user->identity->avatar_url) ?>" id="avatarPreview" class="big-avatar" alt="Avatar">
                
                <div class="form-group" style="text-align: center;">
                    <label for="avatarInput" style="cursor: pointer; color: var(--primary); font-weight: bold;">
                        <i class="fas fa-camera"></i> Загрузить новое фото
                    </label>
                    <input type="file" id="avatarInput" style="display: none;" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Никнейм</label>
                    <input type="text" name="nickname" value="<?= Html::encode(Yii::$app->user->identity->username) ?>" class="input-field">
                </div>

                <div class="form-group">
                    <label>Ваш ID</label>
                    <input type="text" value="#<?= Html::encode(Yii::$app->user->identity->id) ?>" class="input-field" disabled>
                </div>

                <button class="btn" type="button" onclick="alert('Сохранено (демо)!')">Сохранить изменения</button>
            </form>
        </div>

        <!-- стата -->
        <div class="profile-box" style="display: flex; flex-direction: column; justify-content: center;">
            <i class="fas fa-trophy" style="font-size: 50px; color: #FFD700; margin-bottom: 20px;"></i>
            <h3>Ваши достижения</h3>
            <div style="margin-top: 30px; text-align: left;">
                <div class="stat-row">
                    <span>Пройдено квестов:</span>
                    <strong> <?= $completedQuestCount ?> </strong>
                </div>
                <div class="stat-row">
                    <span>Создано квестов:</span>
                    <strong> <?= $createdQuestCount ?> </strong>
                </div>
                <div class="stat-row">
                    <span>Рейтинг:</span>
                    <?php if ($completedQuestCount > 10): ?>
                        <strong style="color: var(--primary);">Трудяга</strong>
                    <?php elseif ($completedQuestCount < 5): ?>
                        <strong style="color: var(--primary);">Бедолага</strong>
                    <?php elseif ($completedQuestCount <= 10): ?>
                        <strong style="color: var(--primary);">Горемыка</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../web/js/script.js"></script>
</body>
</html>