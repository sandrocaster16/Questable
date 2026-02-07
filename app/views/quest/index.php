<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $quests app\models\Quests[] */

$this->title = 'Questable - Мои квесты';
?>

<div class="container py-4 quest-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Мои квесты</h2>
        <a href="<?= Url::to(['create']) ?>" class="btn-nice btn-nice-primary">
            <i class="fas fa-plus"></i> Создать новый
        </a>
    </div>

    <?php if (empty($quests)): ?>
        <div class="card text-center p-5 mt-4">
            <div class="card-body">
                <h3 class="card-title text-muted">Список пуст</h3>
                <p class="card-text">У вас пока нет активных квестов.</p>
                <a href="<?= Url::to(['create']) ?>" class="btn-nice btn-nice-primary mt-3">
                    Создать первый квест
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid-container mt-4">
            <?php foreach ($quests as $quest): ?>
                <a class="quest-card" href="<?= Url::to(['update', 'id' => $quest->id]) ?>">
                    <div class="card-img">
                        <img src="<?= Html::encode($quest->cover_image_url) ?>" alt="<?= Html::encode($quest->name) ?>">
                    </div>
                    <div class="card-text">
                        <h3><?= Html::encode($quest->name) ?></h3>
                        <p><?= Html::encode($quest->description) ?></p>
                        <div class="card-footer">
                            <span class="rating">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
