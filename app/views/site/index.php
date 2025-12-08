<?php
/** @var yii\web\View $this */
/** @var int $id */
/** @var string $avatar_path */
/** @var string $username */
/** @var array $popular_quests */
/** @var array $user_history */

use yii\helpers\Html;

$this->title = 'Questable';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная - Questable</title>
    <link rel="stylesheet" href="../app/web/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container">
    <!-- актуалочка -->
    <h2 style="margin-bottom: 20px;">Популярные квесты</h2>
    <div class="slider">
        <button class="slider-btn prev-btn" id="promo-prev-btn">&lt;</button>
        <div class="slider-wrapper">
            <div class="cards-track" id="promo-track">
                <!-- квестики -->
                <?php foreach ($popular_quests as $quest): ?>
                    <div class="quest-card">
                        <div class="card-img">
                            <img src="<?= $quest['cover_image_url'] ?>" alt="">
                        </div>
                        <div class="card-text">
                            <h3> <?= $quest['name'] ?> </h3>
                            <p> <?= $quest['description'] ?> </p>
<!--                            <div class="card-footer">-->
<!--                                <span class="rating"><i class="fas fa-star"></i> --><?php //= $quest['rating'] ?><!--</span>-->
<!--                            </div>-->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="slider-btn next-btn" id="promo-next-btn">&gt;</button>
    </div>

    <hr style="border:0; border-top:1px solid #ddd; margin: 40px 0;">

    <!-- история посещений -->
    <h2 style="margin-bottom: 20px;">История посещений</h2>

    <?php if (empty($user_history)): ?>
        <!-- если пусто -->
        <div class="empty-block">
            <i class="fas fa-ghost"></i>
            <h3>Пусто</h3>
            <p>Вы ещё не посетили ни один квест.</p>
        </div>

    <?php else: ?>
        <!-- если есть, то обрезка до 6 -->
        <div class="grid-container">
            <?php foreach(array_slice($user_history, 0, 6) as $user_historyquest): ?>
                <div class="quest-card">
                    <div class="card-img">
                        <img src="<?= $user_historyquest['cover_image_url'] ?>" alt="">
                    </div>
                    <div class="card-text">
                        <h3> <?= $user_historyquest['name'] ?> </h3>
                        <p> <?= $user_historyquest['description'] ?> </p>
<!--                        <div class="card-footer">-->
<!--                            <span class="rating"><i class="fas fa-star"></i> --><?php //= $user_historyquest['rating'] ?><!--</span>-->
<!--                        </div>-->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>