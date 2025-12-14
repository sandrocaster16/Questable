<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\forms\StationForm */
?>

<div class="station-form">

    <?php $form = ActiveForm::begin(['id' => 'station-active-form']); ?>

    <div class="row g-3 mb-3">
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Название точки'
            ]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'type')->dropDownList([
                'info' => 'Инфо (Текст / Фото)',
                'quiz' => 'Квиз (Вопрос)',
                'curator_check' => 'Проверка куратором'
            ], [
                'id' => 'station-type-select',
                'class' => 'form-select'
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'content')->textarea([
        'rows' => 4,
        'class' => 'form-control',
        'placeholder' => 'Текст задания или вопроса'
    ]) ?>

    <!-- QUIZ BLOCK -->
    <div id="quiz-block" class="quiz-block card border-0 mb-3" style="display:none;">
        <div class="card-body px-0">
            <h6 class="fw-bold mb-3">Настройки теста</h6>

            <?= $form->field($model, 'answers_raw')->textarea([
                'rows' => 4,
                'class' => 'form-control',
                'placeholder' => "Вариант А\nВариант Б\nВариант В"
            ])->hint('Введите каждый вариант ответа с новой строки.') ?>

            <?= $form->field($model, 'correct_answer')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Скопируйте сюда правильный вариант'
            ]) ?>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <?= Html::submitButton(
            '<i class="fas fa-save me-2"></i> Сохранить станцию',
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
