<?php
/** @var yii\web\View $this */
/** @var int $id */
/** @var string $avatar_path */
/** @var string $username */
/** @var array $popular_quests */
/** @var array $user_history */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="container">
    <!-- актуалочка -->
    <h2>Популярные квесты</h2>
    <div class="slider">
        <button class="slider-btn prev-btn" id="promo-prev-btn">&lt;</button>
        <div class="slider-wrapper">
            <div class="cards-track" id="promo-track">
                <!-- квестики -->
                <?php foreach ($popular_quests as $quest): ?>
                    <!-- TODO: rating -->
                    <a class="quest-card" href="/quest/view?id=<?= $quest['id'] ?>" >
                        <div class="card-img">
                            <img src="<?= $quest['cover_image_url'] ?>" alt="<?= $quest['name'] ?>">
                        </div>
                        <div class="card-text">
                            <h3> <?= $quest['name'] ?> </h3>
                            <p> <?= $quest['description'] ?> </p>
                            <div class="card-footer">
                               <span class="rating"><i class="fas fa-star"></i></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="slider-btn next-btn" id="promo-next-btn">&gt;</button>
    </div>
    <hr>
    <!-- история посещений -->
    <h2>История посещений</h2>
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
                <!-- TODO: href на квест -->
                <!-- TODO: rating -->
                <a class="quest-card"  href="/game/progress?quest_id=<?= $user_historyquest['id'] ?>" >
                    <div class="card-img">
                        <img src="<?= $user_historyquest['cover_image_url'] ?>" alt="<?= $user_historyquest['name'] ?>">
                    </div>
                    <div class="card-text">
                        <h3> <?= $user_historyquest['name'] ?> </h3>
                        <p> <?= $user_historyquest['description'] ?> </p>
                        <div class="card-footer">
                            <span class="rating"><i class="fas fa-star"></i></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>