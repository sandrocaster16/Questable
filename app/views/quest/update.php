<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Редактирование: ' . $quest->name;
?>

    <div class="container py-4">
        <!-- Заголовок страницы -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Конструктор: <?= Html::encode($quest->name) ?></h2>
            <div class="btn-group">
                <a href="<?= Url::to(['index']) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
                <a href="<?= Url::to(['delete', 'id' => $quest->id]) ?>"
                   class="btn btn-danger"
                   data-confirm="Удалить квест?">
                    <i class="fas fa-trash"></i> Удалить квест
                </a>
            </div>
        </div>

        <!-- Блок 1: Основная информация о квесте (Card) -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Основные настройки</h5>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($questForm, 'name')->textInput(['class' => 'form-control'])->label('Название') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($questForm, 'coverFile')->fileInput(['class' => 'form-control'])->label('Сменить обложку') ?>
                    </div>
                </div>

                <?= $form->field($questForm, 'description')->textarea(['rows' => 2, 'class' => 'form-control'])->label('Описание') ?>

                <div class="mt-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <!-- Блок 2: Станции (Card + List Group) -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0">Станции (Точки маршрута)</h5>
                <button class="btn btn-success btn-sm" id="add-station-btn" data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id]) ?>">
                    <i class="fas fa-plus"></i> Добавить станцию
                </button>
            </div>

            <div class="list-group list-group-flush">
                <?php if (empty($stations)): ?>
                    <div class="list-group-item text-center text-muted py-4">
                        В этом квесте пока нет станций. Добавьте первую!
                    </div>
                <?php else: ?>
                    <?php foreach ($stations as $station): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">

                            <!-- Информация о станции -->
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <?= Html::encode($station->name) ?>
                                    <!-- Badge для типа -->
                                    <span class="badge bg-info text-dark ms-2"><?= $station->type ?></span>
                                </div>
                                <?php if($station->type == 'quiz'): ?>
                                    <small class="text-muted">
                                        Ответ: <?= json_decode($station->options, true)['correct_answer'] ?? '-' ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Кнопки действий -->
                            <div class="btn-group btn-group-sm">
                                <a href="<?= Url::to(['game/visit', 'qr' => $station->qr_identifier]) ?>" target="_blank" class="btn btn-outline-secondary" title="Тест QR">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                                <button class="btn btn-outline-primary edit-station-btn"
                                        data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id, 'id' => $station->id]) ?>"
                                        title="Редактировать">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <a href="<?= Url::to(['delete-station', 'id' => $station->id]) ?>"
                                   class="btn btn-outline-danger"
                                   data-confirm="Вы уверены?"
                                   title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Стандартное Модальное окно Bootstrap -->
    <div class="modal fade" id="station-modal" tabindex="-1" aria-labelledby="stationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- modal-lg для широкого окна -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stationModalLabel">Станция</h5>
                    <!-- Крестик закрытия (поддерживает и BS4 и BS5) -->
                    <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none d-sm-block">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="station-modal-body">
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-2">Загрузка...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    // Используем jQuery для управления модальным окном Bootstrap
    
    const stationModalId = '#station-modal';
    const stationBodyId = '#station-modal-body';

    function loadModalContent(url) {
        const modalEl = $(stationModalId);
        const bodyEl = $(stationBodyId);

        // 1. Показываем модальное окно
        modalEl.modal('show');

        // 2. Сбрасываем контент на лоадер
        bodyEl.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2">Загрузка...</p></div>');

        // 3. Загружаем форму
        bodyEl.load(url, function(response, status, xhr) {
            if (status === "error") {
                bodyEl.html('<div class="alert alert-danger">Ошибка загрузки: ' + xhr.status + ' ' + xhr.statusText + '</div>');
            }
        });
    }

    // Клик по "Добавить станцию"
    $('#add-station-btn').on('click', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        loadModalContent(url);
    });

    // Клик по "Редактировать" (делегирование)
    $(document).on('click', '.edit-station-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        loadModalContent(url);
    });
    
    // AJAX отправка формы внутри модалки
    $(document).on('submit', '#station-active-form', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.success) {
                    $(stationModalId).modal('hide'); // Закрываем модалку
                    location.reload(); // Перезагружаем страницу
                } else {
                    // Если сервер вернул ошибки валидации, Yii обычно перерисовывает форму сам, 
                    // но если вы возвращаете JSON, то обработку ошибок нужно добавить здесь.
                    // Для простоты предполагаем, что при ошибке renderAjax вернет HTML с ошибками,
                    // но в текущем контроллере стоит редирект или JSON success.
                    alert('Ошибка сохранения. Проверьте данные.');
                }
            },
            error: function() {
                alert('Произошла ошибка сервера.');
            }
        });
        
        return false;
    });
JS;
$this->registerJs($js);
?>