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
        <a href="<?= Url::to(['create']) ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Создать новый
        </a>
    </div>

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
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php
                        $img = $quest->cover_image_url ?? '/uploads/quest_previews/default.png';
                        ?>
                        <div style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="<?= $img ?>" class="card-img-top" alt="<?= Html::encode($quest->name) ?>" style="object-fit: cover; height: 100%; width: 100%;">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= Html::encode($quest->name) ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?= Html::encode(StringHelper::truncateWords($quest->description, 10)) ?>
                            </p>
                            <a href="<?= Url::to(['update', 'id' => $quest->id]) ?>" class="btn btn-outline-primary mt-auto">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>