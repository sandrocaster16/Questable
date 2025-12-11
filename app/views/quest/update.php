<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Редактирование: ' . $quest->name;

// Формируем URL для QR кода (используем $quest->id, так как переменная называется $quest)
$questUrl = Url::to(['site/view', 'id' => $quest->id], true);

// Подключаем иконки и библиотеку QR кода
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/qrcode@1.5.4/qrcode.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

    <!-- Добавляем локальные стили -->
    <style>
        /* Базовый стиль красивой кнопки */
        .btn-nice {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            border: 2px solid transparent;
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            cursor: pointer;
            text-decoration: none !important;
            width: auto !important;
            margin-top: 0 !important;
            line-height: 1.2;
        }

        .btn-nice:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-nice:active {
            transform: translateY(-1px);
        }

        /* Главная кнопка (Сохранить) - Черная */
        .btn-nice-primary {
            background: #212529;
            color: #fff;
        }
        .btn-nice-primary:hover {
            opacity: 0.9;
            color: #fff;
        }

        /* Вторичная кнопка (Назад) - Белая с рамкой */
        .btn-nice-secondary {
            background: #fff;
            color: #212529;
            border: 2px solid #dee2e6;
        }
        .btn-nice-secondary:hover {
            border-color: #212529;
            background: #fff;
            color: #212529;
        }

        /* Кнопка QR кода - Синяя/Инфо */
        .btn-nice-info {
            background: #e7f1ff;
            color: #0d6efd;
            border: 2px solid #cff4fc;
        }
        .btn-nice-info:hover {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }

        /* Опасная кнопка (Удалить) */
        .btn-nice-danger {
            background: #fff;
            color: #dc2626;
            border: 2px solid #fee2e2;
        }
        .btn-nice-danger:hover {
            border-color: #dc2626;
            background: #fef2f2;
            color: #dc2626;
        }

        /* Кнопка добавления */
        .btn-nice-add {
            background: #fff;
            color: #212529;
            border: 2px dashed #212529;
        }
        .btn-nice-add:hover {
            background: #212529;
            color: #fff;
            border-style: solid;
        }

        /* Маленькие кнопки действий в списке */
        .btn-icon-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            background: #fff;
            color: #6c757d;
            transition: 0.2s;
        }
        .btn-icon-action:hover {
            border-color: #212529;
            color: #212529;
            transform: scale(1.1);
        }
        .btn-icon-action.delete:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="h3 mb-0 fw-bold">Конструктор: <?= Html::encode($quest->name) ?></h2>

            <!-- Кнопки навигации -->
            <div class="header-actions">
                <a href="<?= Url::to(['index']) ?>" class="btn-nice btn-nice-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>

                <!-- Кнопка вызова QR кода -->
                <button type="button" class="btn-nice btn-nice-info" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
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

        <!-- Блок 1: Основная информация -->
        <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #dee2e6; overflow: hidden;">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">Основные настройки</h5>
            </div>
            <div class="card-body p-4">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($questForm, 'name')->textInput(['class' => 'form-control'])->label('Название') ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($questForm, 'coverFile')->fileInput(['class' => 'form-control'])->label('Сменить обложку') ?>
                    </div>
                </div>

                <?= $form->field($questForm, 'description')->textarea(['rows' => 2, 'class' => 'form-control'])->label('Описание') ?>

                <div class="mt-4">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', ['class' => 'btn-nice btn-nice-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <!-- Блок 2: Станции -->
        <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid #dee2e6; overflow: hidden;">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom p-3">
                <h5 class="mb-0 fw-bold">Станции</h5>
                <button class="btn-nice btn-nice-add" id="add-station-btn" data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id]) ?>">
                    <i class="fas fa-plus"></i> Добавить станцию
                </button>
            </div>

            <div class="list-group list-group-flush">
                <?php if (empty($stations)): ?>
                    <div class="list-group-item text-center text-muted py-5 border-0">
                        <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fas fa-map-marker-alt fa-2x text-secondary"></i>
                        </div>
                        <p>В этом квесте пока нет станций.<br>Нажмите "Добавить станцию", чтобы создать первую точку.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($stations as $station): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom-0 border-top">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold fs-5">
                                    <?= Html::encode($station->name) ?>
                                    <?php
                                    $badges = [
                                            'info' => ['text' => 'Инфо', 'class' => 'bg-secondary text-white'],
                                            'quiz' => ['text' => 'Квиз', 'class' => 'bg-dark text-white'],
                                            'curator_check' => ['text' => 'Куратор', 'class' => 'bg-warning text-dark'],
                                    ];
                                    $badge = $badges[$station->type] ?? ['text' => $station->type, 'class' => 'bg-light text-dark border'];
                                    ?>
                                    <span class="badge <?= $badge['class'] ?> ms-2 rounded-pill fw-normal"><?= $badge['text'] ?></span>
                                </div>
                                <?php if($station->type == 'quiz' && $station->options): ?>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-check-circle me-1"></i> Ответ: <?= Html::encode(json_decode($station->options, true)['correct_answer'] ?? '-') ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn-icon-action edit-station-btn"
                                        data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id, 'id' => $station->id]) ?>"
                                        title="Редактировать">
                                    <i class="fas fa-pen fa-sm"></i>
                                </button>
                                <a href="<?= Url::to(['delete-station', 'id' => $station->id]) ?>"
                                   class="btn-icon-action delete"
                                   data-confirm="Удалить станцию?"
                                   data-method="post"
                                   title="Удалить">
                                    <i class="fas fa-trash fa-sm"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Модальное окно для станций -->
    <div class="modal fade" id="station-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Редактирование станции</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="station-modal-body"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для QR кода -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="qrCodeModalLabel">QR код для квеста</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-muted mb-3">Отсканируйте QR код, чтобы открыть квест</p>
                    <div id="qrcode" class="mb-3" style="display: flex; justify-content: center;"></div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="questUrlInput" value="<?= Html::encode($questUrl) ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyUrl(this)">
                            <i class="fas fa-copy"></i> Копировать
                        </button>
                    </div>
                    <button class="btn btn-success" onclick="downloadQR()">
                        <i class="fas fa-download"></i> Скачать QR код
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
// Передаем переменную PHP в JS
$jsData = json_encode($questUrl);
$questId = $quest->id;

$js = <<<JS
    // === ЛОГИКА СТАНЦИЙ ===
    const modalEl = new bootstrap.Modal(document.getElementById('station-modal'));
    const bodyEl = $('#station-modal-body');

    $(document).on('click', '#add-station-btn, .edit-station-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        bodyEl.html('<div class="text-center py-5"><div class="spinner-border text-dark" role="status"></div><p class="mt-2 text-muted">Загрузка...</p></div>');
        modalEl.show();

        bodyEl.load(url, function(response, status, xhr) {
            if (status === "error") {
                bodyEl.html('<div class="alert alert-danger">Ошибка: ' + xhr.status + '</div>');
            } else {
                toggleQuizBlock();
            }
        });
    });

    $(document).on('change', '#station-type-select', function() {
        toggleQuizBlock();
    });

    function toggleQuizBlock() {
        const type = $('#station-type-select').val();
        const quizBlock = $('#quiz-block');
        if(quizBlock.length) {
            type === 'quiz' ? quizBlock.slideDown() : quizBlock.slideUp();
        }
    }
    
    $(document).on('submit', '#station-active-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            processData: false, 
            contentType: false,
            success: function(response) {
                if (response.success) {
                    modalEl.hide();
                    location.reload(); 
                } else {
                    bodyEl.html(response);
                    toggleQuizBlock();
                }
            },
            error: function() {
                alert('Ошибка сохранения');
            }
        });
        return false;
    });

    // === ЛОГИКА QR КОДА ===
    let qrCodeCanvas = null;
    const questUrl = {$jsData};
    const questId = {$questId};

    const qrModalEl = document.getElementById('qrCodeModal');
    
    // Генерируем QR код при открытии модального окна
    qrModalEl.addEventListener('shown.bs.modal', function () {
        const qrcodeDiv = document.getElementById('qrcode');
        
        // Если QR код уже сгенерирован (canvas существует), не пересоздаем его, чтобы не дублировать
        if (qrcodeDiv.querySelector('canvas')) {
            return;
        }
        
        qrcodeDiv.innerHTML = ''; // Очистка на всякий случай
        
        const canvas = document.createElement('canvas');
        qrcodeDiv.appendChild(canvas);
        
        if (typeof QRCode === 'undefined') {
            qrcodeDiv.innerHTML = '<div class="alert alert-warning">Библиотека QR кода не загружена</div>';
            return;
        }

        QRCode.toCanvas(canvas, questUrl, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Ошибка генерации QR кода:', error);
                qrcodeDiv.innerHTML = '<div class="alert alert-danger">Ошибка генерации QR кода</div>';
            } else {
                qrCodeCanvas = canvas;
            }
        });
    });

    // Делаем функции глобальными, чтобы работали onclick в HTML
    window.copyUrl = function(btnElement) {
        const input = document.getElementById('questUrlInput');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const originalText = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fas fa-check"></i> Скопировано!';
        btnElement.classList.remove('btn-outline-secondary');
        btnElement.classList.add('btn-success');
        
        setTimeout(function() {
            btnElement.innerHTML = originalText;
            btnElement.classList.remove('btn-success');
            btnElement.classList.add('btn-outline-secondary');
        }, 2000);
    }

    window.downloadQR = function() {
        if (!qrCodeCanvas) {
            alert('QR код еще не сгенерирован');
            return;
        }
        const link = document.createElement('a');
        link.download = 'quest-qr-' + questId + '.png';
        link.href = qrCodeCanvas.toDataURL('image/png');
        link.click();
    }
JS;
$this->registerJs($js);
?>