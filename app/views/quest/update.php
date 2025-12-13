<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Редактирование: ' . $quest->name;

// URL для самого квеста
$questUrl = Url::to(['site/view', 'id' => $quest->id], true);

// Подключаем ресурсы
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
// Используем вашу библиотеку qrcodejs
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

    <!-- Стили (без изменений) -->
    <style>
        .btn-nice {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 24px; border-radius: 12px; font-weight: 700; font-size: 14px;
            border: 2px solid transparent; transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            cursor: pointer; text-decoration: none !important; width: auto !important; margin-top: 0 !important; line-height: 1.2;
        }
        .btn-nice:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-nice:active { transform: translateY(-1px); }

        .btn-nice-primary { background: #212529; color: #fff; }
        .btn-nice-primary:hover { opacity: 0.9; color: #fff; }

        .btn-nice-secondary { background: #fff; color: #212529; border: 2px solid #dee2e6; }
        .btn-nice-secondary:hover { border-color: #212529; background: #fff; color: #212529; }

        .btn-nice-info { background: #e7f1ff; color: #0d6efd; border: 2px solid #cff4fc; }
        .btn-nice-info:hover { background: #0d6efd; color: #fff; border-color: #0d6efd; }

        .btn-nice-danger { background: #fff; color: #dc2626; border: 2px solid #fee2e2; }
        .btn-nice-danger:hover { border-color: #dc2626; background: #fef2f2; color: #dc2626; }

        .btn-nice-add { background: #fff; color: #212529; border: 2px dashed #212529; }
        .btn-nice-add:hover { background: #212529; color: #fff; border-style: solid; }

        .btn-icon-action {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #dee2e6; background: #fff; color: #6c757d; transition: 0.2s;
            cursor: pointer;
        }
        .btn-icon-action:hover { border-color: #212529; color: #212529; transform: scale(1.1); }
        .btn-icon-action.delete:hover { border-color: #dc2626; color: #dc2626; }
        .btn-icon-action.qr:hover { border-color: #0d6efd; color: #0d6efd; }

        .header-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="h3 mb-0 fw-bold">Конструктор: <?= Html::encode($quest->name) ?></h2>

            <div class="header-actions">
                <a href="<?= Url::to(['index']) ?>" class="btn-nice btn-nice-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>

                <button type="button" class="btn-nice btn-nice-info show-qr-btn"
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

        <!-- Основные настройки -->
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

        <!-- Список станций -->
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
                        <p>В этом квесте пока нет станций.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($stations as $station): ?>
                        <?php
                        $stationUrl = Url::to(['game/visit', 'qr' => $station->qr_identifier], true);
                        ?>
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
                                <button class="btn-icon-action qr show-qr-btn"
                                        data-url="<?= Html::encode($stationUrl) ?>"
                                        data-title="Станция: <?= Html::encode($station->name) ?>"
                                        data-filename="station-<?= $station->id ?>"
                                        title="QR код станции">
                                    <i class="fas fa-qrcode fa-sm"></i>
                                </button>

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

    <!-- Модальное окно для станций (Edit) -->
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
                    <h5 class="modal-title fw-bold" id="qrCodeModalLabel">QR код</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-muted mb-3">Отсканируйте код для перехода</p>

                    <!-- Контейнер для QR, библиотека qrcodejs сама создаст внутри img или canvas -->
                    <div id="qrcode" class="mb-3 d-flex justify-content-center align-items-center" style="min-height: 256px;"></div>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="questUrlInput" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyUrl(this)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <input type="hidden" id="qrFilename" value="qrcode">

                    <button class="btn btn-success" onclick="downloadQR()">
                        <i class="fas fa-download"></i> Скачать PNG
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    // === ЛОГИКА QR КОДА (Адаптировано под qrcodejs) ===
    const qrModalObj = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    let qrcodeObj = null; // Для хранения экземпляра

    $(document).on('click', '.show-qr-btn', function() {
        const url = $(this).data('url');
        const title = $(this).data('title');
        const filename = $(this).data('filename');
        const qrcodeDiv = document.getElementById('qrcode');
        
        // 1. Обновляем тексты
        $('#qrCodeModalLabel').text(title);
        $('#questUrlInput').val(url);
        $('#qrFilename').val(filename);
        
        // 2. Очищаем предыдущий код
        qrcodeDiv.innerHTML = '';

        // 3. Генерируем новый (синтаксис qrcodejs)
        if (typeof QRCode === 'undefined') {
            qrcodeDiv.innerHTML = '<div class="alert alert-warning">Библиотека QR кода не загружена</div>';
        } else {
            // Создаем экземпляр. Библиотека сама добавит canvas/img внутрь qrcodeDiv
            qrcodeObj = new QRCode(qrcodeDiv, {
                text: url,
                width: 256,
                height: 256,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        }

        // 4. Показываем модалку
        qrModalObj.show();
    });

    window.copyUrl = function(btnElement) {
        const input = document.getElementById('questUrlInput');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const icon = btnElement.querySelector('i');
        const originalClass = icon.className;
        
        icon.className = 'fas fa-check';
        btnElement.classList.replace('btn-outline-secondary', 'btn-success');
        
        setTimeout(function() {
            icon.className = originalClass;
            btnElement.classList.replace('btn-success', 'btn-outline-secondary');
        }, 1500);
    }

    window.downloadQR = function() {
        const container = document.getElementById('qrcode');
        // Библиотека создает canvas или img. Ищем любой из них.
        const canvas = container.querySelector('canvas');
        const img = container.querySelector('img');
        
        let dataUrl = '';
        
        if (canvas) {
            dataUrl = canvas.toDataURL("image/png");
        } else if (img && img.src) {
            dataUrl = img.src;
        } else {
            alert('QR код еще не сгенерирован или произошла ошибка');
            return;
        }

        const filename = $('#qrFilename').val() || 'qrcode';
        const link = document.createElement('a');
        link.download = filename + '.png';
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // === ЛОГИКА РЕДАКТИРОВАНИЯ СТАНЦИЙ (Без изменений) ===
    const stationModalEl = new bootstrap.Modal(document.getElementById('station-modal'));
    const stationBodyEl = $('#station-modal-body');

    $(document).on('click', '#add-station-btn, .edit-station-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        stationBodyEl.html('<div class="text-center py-5"><div class="spinner-border text-dark" role="status"></div><p class="mt-2 text-muted">Загрузка...</p></div>');
        stationModalEl.show();
        stationBodyEl.load(url, function(response, status, xhr) {
            if (status === "error") {
                stationBodyEl.html('<div class="alert alert-danger">Ошибка: ' + xhr.status + '</div>');
            } else {
                toggleQuizBlock();
            }
        });
    });

    $(document).on('change', '#station-type-select', function() { toggleQuizBlock(); });

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
                    stationModalEl.hide();
                    location.reload(); 
                } else {
                    stationBodyEl.html(response);
                    toggleQuizBlock();
                }
            },
            error: function() { alert('Ошибка сохранения'); }
        });
        return false;
    });
JS;
$this->registerJs($js);
?>