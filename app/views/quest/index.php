<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $quests app\models\Quests[] */

$this->title = 'Мои квесты';
?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title" style="margin-bottom: 0;">Мои квесты</h2>
        <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Создать новый
        </a>
    </div>

    <hr>

    <?php if (empty($quests)): ?>
        <div class="not-set" style="text-align: center; padding: 50px;">
            <h3>У вас пока нет созданных квестов</h3>
            <p>Нажмите кнопку "Создать новый", чтобы начать.</p>
        </div>
    <?php else: ?>
        <div class="history-grid">
            <?php foreach ($quests as $quest): ?>
                <!-- Ссылка на редактирование оборачивает карточку -->
                <a href="<?= Url::to(['update', 'id' => $quest->id]) ?>" style="text-decoration: none;">
                    <div class="quest-card">
                        <div class="card-img">
                            <!-- Если нет картинки, ставим заглушку -->
                            <img src="<?= $quest->cover_image_url ?: '/uploads/quest_previews/default.png' ?>" alt="">
                        </div>
                        <div class="card-text">
                            <h3><?= Html::encode($quest->name) ?></h3>
                            <p><?= Html::encode($quest->description) ?></p>
                            <div style="margin-top: auto; padding-top: 10px; color: var(--primary-color); font-weight: bold;">
                                <i class="fas fa-edit"></i> Редактировать
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>