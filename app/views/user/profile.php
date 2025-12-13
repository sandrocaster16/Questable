<?php
use yii\helpers\Html;

/** @var $completedQuestCount int */
/** @var $createdQuestCount int */
/** @var $model \app\models\ProfileForm */ // Подсказка для IDE
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - Questable</title>
    <!-- Подключаем CSRF мета-теги для безопасности, если используются ajax запросы -->
    <?= Html::csrfMetaTags() ?>
</head>
<body>

<div class="container">

    <!-- Вывод сообщений об успехе или ошибках -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <!-- Вывод ошибок валидации конкретных полей -->
    <?php if ($model->hasErrors()): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?= Html::errorSummary($model) ?>
        </div>
    <?php endif; ?>

    <h2 style="margin-bottom: 20px;">Настройки профиля</h2>

    <div class="profile-wrap">
        <!-- профиль -->
        <div class="profile-box">
            <!-- Добавляем action и method -->
            <form action="<?= \yii\helpers\Url::to(['user/profile']) ?>" method="POST" enctype="multipart/form-data">
                <!-- Обязательно добавляем CSRF токен -->
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

                <img src="<?= Html::encode(Yii::$app->user->identity->avatar_url ?: '/img/default-avatar.png') ?>" id="avatarPreview" class="big-avatar" alt="Avatar" style="object-fit: cover;">

                <div class="form-group" style="text-align: center;">
                    <label for="avatarInput" style="cursor: pointer; color: var(--primary); font-weight: bold;">
                        <i class="fas fa-camera"></i> Загрузить новое фото
                    </label>
                    <!-- name должен быть ProfileForm[avatar] -->
                    <input type="file" name="ProfileForm[avatar]" id="avatarInput" style="display: none;" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Никнейм</label>
                    <!-- name должен быть ProfileForm[nickname] -->
                    <input type="text" name="ProfileForm[nickname]" value="<?= Html::encode($model->nickname) ?>" class="input-field">
                </div>

                <div class="form-group">
                    <label>Ваш ID</label>
                    <input type="text" value="#<?= Html::encode(Yii::$app->user->identity->id) ?>" class="input-field" disabled>
                </div>

                <!-- Изменили type="button" на type="submit" и убрали onclick -->
                <button class="btn" type="submit">Сохранить изменения</button>
            </form>
        </div>

        <!-- стата (без изменений) -->
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

<!-- Скрипт для предпросмотра картинки -->
<script>
    document.getElementById('avatarInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>