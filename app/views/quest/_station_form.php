<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\forms\StationForm */
?>

<div class="station-form">
    <?php $form = ActiveForm::begin(['id' => 'station-active-form']); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput([
                    'class' => 'form-control',
                    'placeholder' => 'Название точки'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'type')->dropDownList([
                    'info' => 'Инфо (Текст/Фото)',
                    'quiz' => 'Квиз (Вопрос)',
                    'curator_check' => 'Проверка куратором'
            ], [
                    'id' => 'station-type-select',
                    'class' => 'form-select'
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'content')->textarea([
            'rows' => 5,
            'class' => 'form-control',
            'placeholder' => 'Текст задания или вопроса'
    ]) ?>

    <div id="quiz-block" class="card bg-light border-0 mb-3"
         style="display: <?= $model->type === 'quiz' ? 'block' : 'none' ?>;">
        <div class="card-body">
            <h6 class="card-title fw-bold mb-3">Настройки теста</h6>

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

    <div class="d-grid gap-2 mt-3">
        <?= Html::submitButton('Сохранить станцию', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>