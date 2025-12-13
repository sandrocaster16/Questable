<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/** @var $this yii\web\View */
/** @var $quests app\models\Quests[] */

$this->title = 'Мои квесты';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Мои квесты</h2>
    </div>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Создать новый
    </a>

    <?php if (empty($quests)): ?>
        <div class="card text-center p-5 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-muted">Список пуст</h3>
                <p class="card-text">У вас пока нет активных квестов.</p>
                <a href="<?= Url::to(['create']) ?>" class="btn btn-primary mt-3">Создать первый квест</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($quests as $quest): ?>
                <div class="col" style="margin-top: 50px">
                    <a class="quest-card" href="<?= Url::to(['update', 'id' => $quest->id]) ?>">
                        <div class="card-img">
                            <img src="<?= $quest['cover_image_url'] ?>" alt="<?= $quest['name'] ?>">
                        </div>
                        <div class="card-text">
                            <h3> <?= $quest['name'] ?> </h3>
                            <p> <?= $quest['description'] ?> </p>
                            <div class="card-footer">
                                <span class="rating"><i class="fas fa-star"></i><?php //= $quest['rating'] ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>