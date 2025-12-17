<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="overlay" id="overlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="close-btn" id="sidebarClose">&times;</span>
        <h2 class="sidebar-name">Questable</h2>
    </div>
    <ul class="nav-list">
        <li><a href="<?= Url::to(['/']) ?>"><i class="fas fa-home"></i> Главная</a></li>
        <li><a href="<?= Url::to(['/user/profile']) ?>"><i class="fas fa-user-circle"></i> Профиль</a></li>
        <li><a href="<?= Url::to(['/quest/index']) ?>"><i class="fas fa-map"></i> Мои квесты</a></li>
        <li><a href="<?= Url::to(['/site/info']) ?>"><i class="fas fa-info"></i> Информация</a></li>
        <?php if (Yii::$app->user->identity->isAdmin()) : ?>
        <li><a href="<?= Url::to(['/admin/default/index']) ?>"><i class="fas fa-admin"></i> Админ панель</a></li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-footer">
        <button id="themeToggle" class="theme-btn-sidebar">
            <i class="fas fa-moon"></i>
            <span>Тёмная тема</span>
        </button>
    </div>
</nav>

<header class="container">
    <div class="header-wrapper">
        <div class="user-section">
            <img src="<?= Yii::$app->user->identity->avatar_url ?? Yii::$app->params['defaultAvatar']?>" class="header-img" alt="ava">
            <div class="user-info">
                <h3><?= Html::encode(Yii::$app->user->identity->username ?? '') ?></h3>
                <span>ID: <?= Html::encode(Yii::$app->user->identity->id ?? '') ?></span>
            </div>
        </div>

        <div class="logo-btn" id="sidebarToggle" title="Открыть меню">
            <img src="<?= Yii::getAlias(Yii::$app->params['logoPath']) ?>" class="header-img" alt="logo">
        </div>
    </div>
</header>