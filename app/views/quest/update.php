<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $quest app\models\Quests */
/** @var $questForm app\models\forms\QuestForm */
/** @var $stations app\models\QuestStations[] */

$this->title = 'Редактирование: ' . $quest->name;
?>

    <div class="container">
        <div class="page-header">
            <h2 class="section-title">Конструктор: <?= Html::encode($quest->name) ?></h2>
            <div>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-secondary">Назад</a>
                <a href="<?= Url::to(['delete', 'id' => $quest->id]) ?>" class="btn btn-danger" data-confirm="Удалить квест?">Удалить квест</a>
            </div>
        </div>

        <!-- Блок 1: Основная информация о квесте -->
        <div class="quest-form-container">
            <h3>Основные настройки</h3>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <?= $form->field($questForm, 'name')->label('Название') ?>
                <?= $form->field($questForm, 'coverFile')->fileInput()->label('Сменить обложку') ?>
            </div>
            <?= $form->field($questForm, 'description')->textarea(['rows' => 2])->label('Описание') ?>

            <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>

        <!-- Блок 2: Станции -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="section-title" style="margin: 0;">Станции (Точки маршрута)</h3>
                <button class="btn btn-primary" id="add-station-btn" data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id]) ?>">
                    <i class="fas fa-plus"></i> Добавить станцию
                </button>
            </div>

            <div class="stations-list">
                <?php if (empty($stations)): ?>
                    <p class="not-set">В этом квесте пока нет станций. Добавьте первую!</p>
                <?php else: ?>
                    <?php foreach ($stations as $station): ?>
                        <div class="station-item">
                            <div class="station-info">
                                <h4><?= Html::encode($station->name) ?></h4>
                                <span class="station-type-badge"><?= $station->type ?></span>
                                <?php if($station->type == 'quiz'): ?>
                                    <small style="color: #666; margin-left: 10px;">
                                        (Ответ: <?= json_decode($station->options, true)['correct_answer'] ?? '-' ?>)
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="station-actions">
                                <!-- Кнопка QR -->
                                <a href="<?= Url::to(['game/visit', 'qr' => $station->qr_identifier]) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Тест игры">
                                    <i class="fas fa-qrcode"></i> QR
                                </a>
                                <!-- Кнопка Редактировать (AJAX) -->
                                <button class="btn btn-secondary btn-sm edit-station-btn"
                                        data-url="<?= Url::to(['save-station', 'quest_id' => $quest->id, 'id' => $station->id]) ?>">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <!-- Кнопка Удалить -->
                                <a href="<?= Url::to(['delete-station', 'id' => $station->id]) ?>"
                                   class="btn btn-danger btn-sm"
                                   data-confirm="Вы уверены?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Модальное окно для Станций (используем ту же структуру, что в layout, но отдельный ID) -->
    <div id="station-modal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Станция</h2>
                <span class="close-modal-btn" id="close-station-modal">&times;</span>
            </div>
            <div class="modal-body" id="station-modal-body">
                <!-- Сюда загрузится форма через AJAX -->
                <div style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    const stationModal = document.getElementById('station-modal');
    const stationBody = $('#station-modal-body'); // Используем jQuery для тела
    
    function openStationModal(url) {
        // 1. Открываем окно (добавляем класс CSS)
        stationModal.classList.add('open');
        
        // 2. Показываем лоадер
        stationBody.html('<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>');
        
        // 3. Загружаем форму через jQuery .load()
        // Это автоматически выполнит все скрипты (ActiveForm), которые придут с сервера
        stationBody.load(url, function(response, status, xhr) {
            if (status == "error") {
                stationBody.html('<div class="alert alert-danger">Ошибка загрузки: ' + xhr.status + ' ' + xhr.statusText + '</div>');
            }
        });
    }

    function closeStationModal() {
        stationModal.classList.remove('open');
    }

    // Открытие на создание
    $('#add-station-btn').on('click', function(e) {
        e.preventDefault();
        // Берем URL из data-атрибута кнопки
        let url = $(this).data('url');
        openStationModal(url);
    });

    // Открытие на редактирование (делегирование событий для динамических кнопок)
    $(document).on('click', '.edit-station-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        openStationModal(url);
    });

    // Закрытие по крестику
    $('#close-station-modal').on('click', closeStationModal);
    
    // Закрытие по клику вне окна (оверлей)
    window.onclick = function(event) {
        if (event.target == stationModal) {
            closeStationModal();
        }
    }
    
    // Обработка отправки формы внутри модалки
    // Мы слушаем submit на document, так как форма подгружается динамически
    $(document).on('submit', '#station-active-form', function(e) {
        e.preventDefault();
        var form = $(this);
        
        // Отправляем данные через AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.success) {
                    location.reload(); // Перезагружаем страницу при успехе
                } else {
                    alert('Ошибка сохранения. Проверьте правильность заполнения полей.');
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