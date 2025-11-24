<?php
/** @var yii\web\View $this */
/** @var int $id */
/** @var string $avatar_path */
/** @var string $logo_path */
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
    <title> <?= $this->title ?> </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="overlay" id="overlay"></div>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="close-btn" id="close-btn">&times;</span>
            <div class="sidebar-logo" id="sidebar-logo"></div>
        </div>
        <ul class="nav-links">
            <li><a href="/">Главная</a></li>
            <hr>
            <li><a href="/quest/create">Создать квест</a></li>
            <hr>
            <li><a href="/info">О проекте</a></li>

            <!-- TODO: не будет отобрадаться у не админа -->
            <hr>
            <li><a href="/admin-panel">Админ панель</a></li>
        </ul>
    </nav>

    <div class="container">
        <header class="header">
            <div class="avatar">
                <img src="<?= $avatar_path ?>" alt="avatar">
            </div>
            <div class="user-profile-section">
                <div class="user-info">
                    <p class="nickname"><?= $username ?></p>
                    <p class="user-id">ID: <?= $id ?> </p>
                    <div class="settings-icon" id="settings-btn">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>
            <div class="logo" id="logoBtn">
                <img src="<?= Html::encode($logo_path) ?>" alt="logo">
            </div>
        </header>

        <div id="settings-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Настройки профиля</h2>
                    <span class="close-modal-btn" id="close-modal-btn">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="form-group avatar-upload-container">
                        <label for="avatar-input">Аватар</label>
                        <img src="https://via.placeholder.com/100" alt="avatar preview" id="avatar-preview" class="avatar-preview">
                        <input type="file" id="avatar-input" accept="image/*" style="display: none;">
                        <button onclick="document.getElementById('avatar-input').click();" class="btn btn-secondary">Выбрать файл</button>
                    </div>
                    <div class="form-group">
                        <label for="nickname-input">Никнейм</label>
                        <input type="text" id="nickname-input" placeholder="Введите новый никнейм" value="<?= $username ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="cancel-settings-btn" class="btn btn-secondary">Отмена</button>
                    <button id="save-settings-btn" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>

        <hr>

        <!-- актуальные квесты -->
        <section>
            <h2 class="section-title">Успейте посетить</h2>
            <div class="slider">
                <button class="slider-btn prev-btn" id="promo-prev-btn">&lt;</button>
                <div class="slider-wrapper">
                    <div class="cards-track" id="promo-track">
                        <!-- прод -->
                        <?php foreach ($popular_quests as $quest): ?>
                            <div class="quest-card">
                                <div class="card-img">
                                    <img src="<?= $quest['cover_image_url'] ?>" alt="">
                                </div>
                                <div class="card-text">
                                    <h3> <?= $quest['name'] ?> </h3>
                                    <p> <?= $quest['description'] ?> </p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- для показа кудлису (потом удалить) -->
                        <!-- TODO: имеется баг с картинками -->
                        <div class="quest-card">
                            <div class="card-img">
                                <img src="https://s3.stroi-news.ru/img/krasivie-kartinki-peizazh-1.jpg" alt="">
                            </div>
                            <div class="card-text">
                                <h3>Тест 1</h3>
                                <p>Описание</p>
                            </div>
                        </div>
                        <div class="quest-card">
                            <div class="card-img">
                                <img src="https://www.russiadiscovery.ru/storage/images/resized/posts/1493/poster/422/original_1220.jpg" alt="">
                            </div>
                            <div class="card-text">
                                <h3>Тест 2</h3>
                                <p>Описание</p>
                            </div>
                        </div>
                        <div class="quest-card">
                            <div class="card-img">
                                <img src="https://cs8.pikabu.ru/post_img/big/2016/10/30/5/1477812607183074526.jpg" alt="">
                            </div>
                            <div class="card-text">
                                <h3>Тест 3</h3>
                                <p>Описание</p>
                            </div>
                        </div>
                        <div class="quest-card">
                            <div class="card-img">
                                <img src="https://kartin.papik.pro/uploads/posts/2023-06/thumbs/1686949622_kartin-papik-pro-p-kartinki-krasivie-s-zhivotnimi-s-tsvetami-37.jpg" alt="">
                            </div>
                            <div class="card-text">
                                <h3>Тест 4</h3>
                                <p>Описание</p>
                            </div>
                        </div>

                    </div>
                </div>
                <button class="slider-btn next-btn" id="promo-next-btn">&gt;</button>
            </div>
        </section>

        <hr>

        <!-- история -->
        <section class="section" id="history-section">
            <h2 class="section-title">История посещений</h2>
            <?php if (!empty($user_history)): ?>
                <div class="history-grid">
                    <?php foreach ($user_history as $quest): ?>
                        <div class="quest-card">
                            <div class="card-img">
                                <img src="<?= $quest['cover_image_url'] ?>" alt="">
                            </div>
                            <div class="card-text">
                                <h3> <?= $quest['name'] ?> </h3>
                                <p> <?= $quest['description'] ?> </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
<!--            TODO: дописать пустую историю-->
                <h2 class="section-title">Пусто</h2>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>