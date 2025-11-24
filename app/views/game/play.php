<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;

/** @var $station app\models\QuestStations */
/** @var $participant app\models\QuestParticipants */
/** @var $progress app\models\StationProgress|null */

$this->title = $station->name;
$isCompleted = $progress && $progress->status == 'completed';
?>

<div class="container" style="padding-top: 20px; padding-bottom: 40px;">

    <div class="game-container">
        <div class="game-header">
            <h2 style="margin: 0; font-size: 20px;"><?= Html::encode($station->name) ?></h2>
        </div>

        <div class="game-content">
            <!-- Флеш сообщения -->
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>

            <!-- Если станция пройдена -->
            <?php if ($isCompleted): ?>
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-trophy" style="font-size: 60px; color: #ffc107; margin-bottom: 20px;"></i>
                    <h3>Станция пройдена!</h3>
                    <p>Вы молодцы. Ищите следующий QR-код.</p>
                </div>

                <!-- Если НЕ пройдена -->
            <?php else: ?>

                <!-- Контент задания -->
                <div class="markdown-content">
                    <?= Markdown::process($station->content) ?>
                </div>

                <hr>

                <!-- Логика Квиза -->
                <?php if ($station->type === 'quiz'): ?>
                    <?php $options = json_decode($station->options, true)['options'] ?? []; ?>

                    <form method="post" action="<?= Url::to(['submit-answer']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <input type="hidden" name="station_id" value="<?= $station->id ?>">

                        <h3>Выберите ответ:</h3>
                        <div class="quiz-options">
                            <?php foreach ($options as $idx => $opt): ?>
                                <div style="position: relative;">
                                    <input type="radio" name="answer" id="opt_<?= $idx ?>" value="<?= Html::encode($opt) ?>" class="quiz-input" required>
                                    <label for="opt_<?= $idx ?>" class="quiz-label">
                                        <?= Html::encode($opt) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 15px; font-size: 18px;">
                            Ответить
                        </button>
                    </form>

                    <!-- Логика Инфо (должна была авто-комплитнуться, но на всякий случай кнопка) -->
                <?php elseif ($station->type === 'info'): ?>
                    <a href="<?= Url::to(['site/index']) ?>" class="btn btn-secondary" style="width: 100%;">Вернуться на главную</a>

                <?php elseif ($station->type === 'curator_check'): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-user-clock"></i> Покажите экран куратору для подтверждения.
                    </div>
                    <!-- Здесь можно добавить кнопку "Обновить статус" для AJAX проверки -->
                    <button onclick="location.reload()" class="btn btn-secondary" style="width: 100%;">Я проверил, обновить</button>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <div class="game-footer">
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #666;">
                <span>Участник: <?= Html::encode($participant->user_id) // или username ?></span>
                <span>Очки: <b><?= $participant->points ?></b></span>
            </div>
        </div>
    </div>
</div>