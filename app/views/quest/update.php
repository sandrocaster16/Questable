<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Редактирование: ' . $quest->name;
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

    <!-- Добавляем локальные стили, чтобы не трогать глобальный CSS -->
    <style>
        /* Базовый стиль красивой кнопки */
        .btn-nice {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 12px; /* Чуть меньше основного радиуса 14px */
            font-weight: 700;
            font-size: 14px;
            border: 2px solid transparent;
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            cursor: pointer;
            text-decoration: none !important;
            /* Сброс ширины 100% из вашего глобального .btn */
            width: auto !important;
            margin-top: 0 !important;
            line-height: 1.2;
        }

        .btn-nice:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .btn-nice:active {
            transform: translateY(-1px);
        }

        /* Главная кнопка (Сохранить) - Черная */
        .btn-nice-primary {
            background: var(--text-main);
            color: var(--primary-inv);
            box-shadow: var(--shadow-base);
        }
        .btn-nice-primary:hover {
            opacity: 0.9;
            color: var(--primary-inv);
        }

        /* Вторичная кнопка (Назад) - Белая с рамкой */
        .btn-nice-secondary {
            background: var(--bg-surface);
            color: var(--text-main);
            border: 2px solid var(--border);
        }
        .btn-nice-secondary:hover {
            border-color: var(--text-main);
            background: var(--bg-surface);
            color: var(--text-main);
        }

        /* Опасная кнопка (Удалить) - Белая с красным акцентом */
        .btn-nice-danger {
            background: var(--bg-surface);
            color: #dc2626; /* Красный оттенок */
            border: 2px solid #fee2e2;
        }
        .btn-nice-danger:hover {
            border-color: #dc2626;
            background: #fef2f2;
            color: #dc2626;
        }

        /* Кнопка добавления - Пунктирная или Акцентная */
        .btn-nice-add {
            background: var(--bg-surface);
            color: var(--text-main);
            border: 2px dashed var(--text-main);
        }
        .btn-nice-add:hover {
            background: var(--text-main);
            color: var(--primary-inv);
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
            border: 1px solid var(--border);
            background: var(--bg-surface);
            color: var(--text-secondary);
            transition: 0.2s;
        }
        .btn-icon-action:hover {
            border-color: var(--text-main);
            color: var(--text-main);
            transform: scale(1.1);
        }
        .btn-icon-action.delete:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        /* Правки сетки хедера */
        .header-actions {
            display: flex;
            gap: 12px;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 fw-bold">Конструктор: <?= Html::encode($quest->name) ?></h2>

            <!-- Обновленные кнопки навигации -->
            <div class="header-actions">
                <a href="<?= Url::to(['index']) ?>" class="btn-nice btn-nice-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
                <a href="<?= Url::to(['delete', 'id' => $quest->id]) ?>"
                   class="btn-nice btn-nice-danger"
                   data-confirm="Вы уверены, что хотите удалить этот квест?"
                   data-method="post">
                    <i class="fas fa-trash"></i> Удалить
                </a>
            </div>
        </div>

        <!-- Блок 1: Основная информация -->
        <div class="card mb-4 shadow-sm" style="border-radius: var(--border-radius); border: 1px solid var(--border); overflow: hidden;">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">Основные настройки</h5>
            </div>
            <div class="card-body p-4">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($questForm, 'name')->textInput(['class' => 'form-control input-field'])->label('Название') ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <!-- Стилизация инпута файла через Bootstrap класс, но можно добавить input-field -->
                        <?= $form->field($questForm, 'coverFile')->fileInput(['class' => 'form-control input-field'])->label('Сменить обложку') ?>
                    </div>
                </div>

                <?= $form->field($questForm, 'description')->textarea(['rows' => 2, 'class' => 'form-control input-field'])->label('Описание') ?>

                <div class="mt-4">
                    <!-- Кнопка сохранения -->
                    <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', ['class' => 'btn-nice btn-nice-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <!-- Блок 2: Станции -->
        <div class="card shadow-sm" style="border-radius: var(--border-radius); border: 1px solid var(--border); overflow: hidden;">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom p-3">
                <h5 class="mb-0 fw-bold">Станции</h5>
                <!-- Кнопка добавления -->
                <button class="btn-nice btn-nice-add" id="add-station-btn" data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id]) ?>">
                    <i class="fas fa-plus"></i> Добавить станцию
                </button>
            </div>

            <div class="list-group list-group-flush">
                <?php if (empty($stations)): ?>
                    <div class="list-group-item text-center text-muted py-5 border-0">
                        <div style="width: 80px; height: 80px; background: var(--bg-element); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
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

                            <!-- Маленькие кнопки действий -->
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

    <!-- Модальное окно (оставил как было, только Bootstrap классы) -->
    <div class="modal fade" id="station-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--border-radius);">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Редактирование станции</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="station-modal-body"></div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    const modalEl = new bootstrap.Modal(document.getElementById('station-modal'));
    const bodyEl = $('#station-modal-body');

    // Открытие модалки
    $(document).on('click', '#add-station-btn, .edit-station-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        bodyEl.html('<div class="text-center py-5"><div class="spinner-border text-dark" role="status"></div><p class="mt-2 text-muted">Загрузка...</p></div>');
        modalEl.show();

        bodyEl.load(url, function(response, status, xhr) {
            if (status === "error") {
                bodyEl.html('<div class="alert alert-danger">Ошибка: ' + xhr.status + '</div>');
            } else {
                toggleQuizBlock(); // Инициализация состояния
            }
        });
    });

    // Логика переключения типа
    $(document).on('change', '#station-type-select', function() {
        toggleQuizBlock();
    });

    function toggleQuizBlock() {
        const type = $('#station-type-select').val();
        const quizBlock = $('#quiz-block');
        type === 'quiz' ? quizBlock.slideDown() : quizBlock.slideUp();
    }
    
    // AJAX отправка
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
JS;
$this->registerJs($js);
?>