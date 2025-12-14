<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Questable - Редактирование ' . $quest->name;

$questUrl = Url::to(['quest/view', 'id' => $quest->id], true);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js',
    ['position' => \yii\web\View::POS_HEAD]
);
?>

<div class="container py-4 quest-update">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="h3 mb-0 fw-bold">
            Конструктор: <?= Html::encode($quest->name) ?>
        </h2>

        <div class="header-actions">
            <a href="<?= Url::to(['index']) ?>" class="btn-nice btn-nice-secondary">
                <i class="fas fa-arrow-left"></i> Назад
            </a>

            <button type="button"
                    class="btn-nice btn-nice-info show-qr-btn"
                    data-url="<?= Html::encode($questUrl) ?>"
                    data-title="Квест: <?= Html::encode($quest->name) ?>"
                    data-filename="quest-<?= $quest->id ?>">
                <i class="fas fa-qrcode"></i> QR код
            </button>

            <a href="<?= Url::to(['delete', 'id' => $quest->id]) ?>"
               class="btn-nice btn-nice-danger"
               data-confirm="Вы уверены, что хотите удалить этот квест?"
               data-method="post">
                <i class="fas fa-trash"></i> Удалить
            </a>
        </div>
    </div>

    <!-- ОСНОВНЫЕ НАСТРОЙКИ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold">Основные настройки</h5>
        </div>

        <div class="card-body p-4">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $form->field($questForm, 'name')->textInput()->label('Название') ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $form->field($questForm, 'coverFile')->fileInput()->label('Сменить обложку') ?>
                </div>
            </div>

            <?= $form->field($questForm, 'description')
                ->textarea(['rows' => 2])
                ->label('Описание') ?>

            <div class="mt-4">
                <?= Html::submitButton(
                    '<i class="fas fa-save"></i> Сохранить изменения',
                    ['class' => 'btn-nice btn-nice-primary']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- СТАНЦИИ -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 fw-bold">Станции</h5>

            <button class="btn-nice btn-nice-add"
                    id="add-station-btn"
                    data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id]) ?>">
                <i class="fas fa-plus"></i> Добавить станцию
            </button>
        </div>

        <div class="list-group list-group-flush">
            <?php if (empty($stations)): ?>
                <div class="list-group-item text-center py-5">
                    <p class="text-muted">В этом квесте пока нет станций.</p>
                </div>
            <?php else: ?>
                <?php foreach ($stations as $station): ?>
                    <?php $stationUrl = Url::to(['game/visit', 'qr' => $station->qr_identifier], true); ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div class="me-auto">
                            <div class="fw-bold fs-5">
                                <?= Html::encode($station->name) ?>
                            </div>

                            <?php if ($station->type === 'quiz' && $station->options): ?>
                                <small class="text-muted">
                                    Ответ:
                                    <?= Html::encode(json_decode($station->options, true)['correct_answer'] ?? '-') ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn-icon-action qr show-qr-btn"
                                    data-url="<?= Html::encode($stationUrl) ?>"
                                    data-title="Станция: <?= Html::encode($station->name) ?>"
                                    data-filename="station-<?= $station->qr_identifier ?>">
                                <i class="fas fa-qrcode"></i>
                            </button>

                            <button class="btn-icon-action edit-station-btn"
                                    data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id, 'id' => $station->id]) ?>">
                                <i class="fas fa-pen"></i>
                            </button>

                            <a href="<?= Url::to(['delete-station', 'id' => $station->id]) ?>"
                               class="btn-icon-action delete"
                               data-confirm="Удалить станцию?"
                               data-method="post">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- =========================
     QR MODAL
     ========================= -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content qr-modal">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="qrCodeModalLabel">QR код</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center pt-2">
                <div class="qr-wrapper mb-3">
                    <div id="qrcode"></div>
                </div>

                <input type="hidden" id="qrFilename">

                <div class="input-group mb-3">
                    <input type="text"
                           id="questUrlInput"
                           class="form-control text-center"
                           readonly>
                    <button class="btn btn-outline-secondary"
                            onclick="copyUrl(this)">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <button class="btn btn-success w-100"
                        onclick="downloadQR()">
                    <i class="fas fa-download me-2"></i> Скачать QR
                </button>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     STATION MODAL
     ========================= -->
<div class="modal fade" id="station-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content station-modal">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Станция</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="station-modal-body">
                <!-- AJAX CONTENT -->
            </div>

        </div>
    </div>
</div>