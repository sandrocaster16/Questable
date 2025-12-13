<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;

/** @var $station app\models\QuestStations */
/** @var $participant app\models\QuestParticipants */
/** @var $progress app\models\StationProgress|null */
/** @var $questProgress array */
/** @var $isLastStation bool */
/** @var $nextStation app\models\QuestStations */

$this->title = $station->name;
$isCompleted = $progress && $progress->isStatusCompleted();

$quizData = [];
$answersList = [];

if ($station->type === 'quiz' && !empty($station->options)) {
    $quizData = json_decode($station->options, true);
    $answersList = $quizData['answers'] ?? [];
}
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm border">
        <div>
            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Станция</h6>
            <h1 class="h5 mb-0 fw-bold"><?= Html::encode($station->name) ?></h1>
        </div>
        <div class="text-center ps-3 border-start">
            <small class="text-muted d-block" style="font-size: 0.75rem;">Баллы</small>
            <span class="badge bg-primary rounded-pill fs-6"><?= $participant->points ?? 0 ?></span>
        </div>
    </div>

    <?php if (isset($questProgress)): ?>
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold">Прогресс квеста</span>
                <span class="badge bg-success"><?= $questProgress['completed_stations'] ?>/<?= $questProgress['total_stations'] ?></span>
            </div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: <?= $questProgress['progress_percentage'] ?>%" 
                     aria-valuenow="<?= $questProgress['progress_percentage'] ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
            <div class="text-center mt-2">
                <small class="text-muted"><?= $questProgress['progress_percentage'] ?>% завершено</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle fs-3 me-3"></i>
            <div><?= Yii::$app->session->getFlash('success') ?></div>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-times-circle fs-3 me-3"></i>
            <div><?= Yii::$app->session->getFlash('error') ?></div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="prose mb-4 text-break">
                <?= Markdown::process($station->content) ?>
            </div>

            <hr class="my-4 opacity-25">

            <?php if ($isCompleted): ?>

                <div class="text-center py-3 animate__animated animate__fadeIn">
                    <div class="mb-3">
                        <i class="fas fa-star text-warning fa-4x drop-shadow"></i>
                    </div>
                    <h3 class="h4 text-success fw-bold">Задание выполнено!</h3>
                    <p class="text-muted">Двигайтесь к следующей точке.</p>
                </div>

            <?php else: ?>
                <?php if ($station->type === 'quiz'): ?>
                    <form method="post" action="<?= Url::to(['game/submit-answer']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                        <input type="hidden" name="station_id" value="<?= $station->id ?>">

                        <p class="fw-bold mb-3">Выберите вариант ответа:</p>

                        <div class="d-grid gap-2">
                            <?php foreach ($answersList as $index => $answerText): ?>
                                <input type="radio" class="btn-check" name="answer" id="opt_<?= $index ?>" value="<?= Html::encode($answerText) ?>" autocomplete="off" required>
                                <label class="btn btn-outline-primary text-start p-3 fw-semibold" for="opt_<?= $index ?>" style="border-radius: 12px; border-width: 2px;">
                                    <?= Html::encode($answerText) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold mx-auto d-block" style="border-radius: 12px; min-width: 200px; padding: 12px 30px;">
                                <i class="fas fa-paper-plane me-2"></i> Ответить
                            </button>
                        </div>
                    </form>

                <?php elseif ($station->type === 'curator_check'): ?>
                    <div class="text-center bg-light p-4 rounded-3 border border-warning border-2 border-dashed">
                        <i class="fas fa-user-clock text-warning mb-3 fa-3x"></i>
                        <h5 class="fw-bold">Требуется проверка</h5>
                        <p class="mb-3 text-muted small">
                            Покажите этот экран волонтеру. Он подтвердит выполнение задания.
                        </p>
                        <button onclick="location.reload()" class="btn btn-warning fw-bold mx-auto d-block" style="min-width: 200px; padding: 12px 30px; border-radius: 12px;">
                            <i class="fas fa-sync-alt me-2"></i> Проверить статус
                        </button>
                    </div>

                <?php endif; ?>
            <?php endif; ?>

            <div class="text-center mt-4">
                <?php if ($isLastStation): ?>
                    <a href="<?= Url::to(['game/completion', 'quest_id' => $station->quest_id]) ?>" class="btn btn-success btn-lg fw-bold">
                        Завершить квест
                    </a>
                <?php else: ?>
                    <a href="<?= Url::to(['game/progress', 'quest_id' => $station->quest_id]) ?>" class="btn btn-primary btn-lg fw-bold">
                        Далее
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?= Url::to(['site/index']) ?>" class="text-muted text-decoration-none small">
            <i class="fas fa-arrow-left"></i> На главную
        </a>
    </div>
</div>

<style>
    .prose img { max-width: 100%; height: auto; border-radius: 12px; margin: 10px 0; }
    .prose p { margin-bottom: 1rem; line-height: 1.6; }
    .drop-shadow { filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }

    .btn-check:checked + .btn-outline-primary {
        background-color: var(--bs-primary);
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
    }
    .btn-outline-primary { transition: all 0.2s; }
</style>