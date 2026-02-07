<?php
use yii\helpers\Html;
/** @var $completedQuestCount int */
/** @var $createdQuestCount int */
$this->title = 'Questable - Профиль';
?>
<div class="container">
    <h2>Настройки профиля</h2>
    <div class="profile-wrap">
        <!-- профиль -->
        <div class="profile-box">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
            <?php elseif (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
            <?php endif; ?>

            <?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>
                <img src="<?= Html::encode(Yii::$app->user->identity->avatar_url) ?>" id="avatarPreview" class="big-avatar" alt="Avatar">

                <div class="form-group">
                    <label for="avatarInput">
                        <i class="fas fa-camera"></i> Загрузить новое фото
                    </label>
                    <input type="file" id="avatarInput" name="ProfileForm[avatar]" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Никнейм</label>
                    <input type="text" name="ProfileForm[nickname]" value="<?= Html::encode($model->nickname) ?>" class="input-field" required>
                </div>
                <div class="form-group">
                    <label>Ваш ID</label>
                    <input type="text" value="#<?= Html::encode(Yii::$app->user->identity->id) ?>" class="input-field" disabled>
                </div>
                <button class="btn" type="submit">Сохранить изменения</button>
            <?= Html::endForm() ?>
            <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'logout-form']) ?>
                <button class="btn" type="submit">
                    <i class="fas fa-sign-out-alt"></i> Выйти из аккаунта
                </button>
            <?= Html::endForm() ?>
        </div>
        <!-- стата -->
        <div class="profile-box">
            <i class="fas fa-trophy drop-shadow"></i>
            <h3>Ваши достижения</h3>
            <div>
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
                        <strong>Трудяга</strong>
                    <?php elseif ($completedQuestCount < 5): ?>
                        <strong>Бедолага</strong>
                    <?php elseif ($completedQuestCount <= 10): ?>
                        <strong>Горемыка</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>