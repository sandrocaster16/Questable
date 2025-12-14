<?php
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $quest app\models\Quests */
/** @var $participant app\models\QuestParticipants */
/** @var $progress array */
$this->title = 'Questable - Квест завершен: ' . Html::encode($quest->name);
?>
<div class="completion-container">
    <div class="trophy-icon">
        <i class="fas fa-trophy"></i>
    </div>
   
    <h1 class="completion-title">Поздравляем!</h1>
    <p class="completion-subtitle">Вы успешно завершили квест<br><strong><?= Html::encode($quest->name) ?></strong></p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $progress['completed_stations'] ?></div>
            <div class="stat-label">Станций пройдено</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $participant->points ?></div>
            <div class="stat-label">Набрано очков</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $progress['progress_percentage'] ?>%</div>
            <div class="stat-label">Прогресс</div>
        </div>
    </div>
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Отличная работа!</strong> Вы прошли все станции квеста и набрали <?= $participant->points ?> очков.
    </div>
    <div class="action-buttons">
        <a href="<?= Url::to(['game/progress', 'quest_id' => $quest->id]) ?>"
           class="btn btn-primary btn-completion">
            <i class="fas fa-chart-line me-2"></i> Подробная статистика
        </a>
        <a href="<?= Url::to(['site/index']) ?>"
           class="btn btn-outline-primary btn-completion">
            <i class="fas fa-home me-2"></i> На главную
        </a>
    </div>
</div>