<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\forms\StationForm */
?>

    <div class="station-form">
        <?php $form = ActiveForm::begin(['id' => 'station-active-form']); ?>

        <?= $form->field($model, 'name')->textInput(['placeholder' => 'Например: Фонтан Дружбы'])->label('Название точки') ?>

        <?= $form->field($model, 'type')->dropDownList([
            'info' => 'Инфо (Просто текст/фото)',
            'quiz' => 'Квиз (Вопрос с вариантами)',
            'curator_check' => 'Проверка куратором'
        ], ['id' => 'station-type-select'])->label('Тип задания') ?>

        <?= $form->field($model, 'content')->textarea(['rows' => 4, 'placeholder' => 'Текст задания или вопроса. Поддерживает Markdown.'])->label('Текст / Вопрос') ?>

        <!-- Блок для Квиза -->
        <div id="quiz-block" style="display: <?= $model->type === 'quiz' ? 'block' : 'none' ?>; background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <h4>Настройки теста</h4>
            <p style="font-size: 0.9em; color: #666;">Введите варианты ответов через запятую или каждый с новой строки.</p>

            <!-- В MVP сделаем просто textarea для вариантов для простоты парсинга, или несколько инпутов -->
            <div class="form-group">
                <label>Варианты ответов</label>
                <?php
                // Если варианты - массив, соберем в строку для редактирования
                $val = is_array($model->answers) ? implode("\n", $model->answers) : '';
                ?>
                <?= Html::textarea('StationForm[answers_raw]', $val, [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => "Вариант А\nВариант Б\nВариант В"
                ]) ?>
                <small>Каждый ответ с новой строки.</small>
            </div>

            <?= $form->field($model, 'correct_answer')->textInput(['placeholder' => 'Скопируйте сюда правильный ответ'])->label('Правильный ответ (текст)') ?>
        </div>

        <div class="modal-footer" style="padding: 0; padding-top: 15px; background: none; border: none;">
            <?= Html::submitButton('Сохранить станцию', ['class' => 'btn btn-primary', 'style' => 'width: 100%']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <script>
        // Простой скрипт переключения полей
        document.getElementById('station-type-select').addEventListener('change', function() {
            const quizBlock = document.getElementById('quiz-block');
            if(this.value === 'quiz') {
                quizBlock.style.display = 'block';
            } else {
                quizBlock.style.display = 'none';
            }
        });
    </script>

<?php
// Важно: В контроллере в actionSaveStation нужно обработать answers_raw -> explode("\n", ...) -> $model->answers
?>