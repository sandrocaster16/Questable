<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\forms\StationForm */
?>

<div class="station-form">

    <?php $form = ActiveForm::begin([
            'id' => 'station-active-form',
            'action' => [
                    'quest/save-station',
                    'quest_id' => $model->quest_id,
                    'id' => $model->id,
            ],
    ]); ?>

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

    <?= $form->field($model, 'quest_id')->hiddenInput()->label(false) ?>

    <!-- QUIZ BLOCK -->
    <div id="quiz-block" class="quiz-block card border-0 mb-3" style="display:none;">
        <div class="card-body px-0">
            <h6 class="fw-bold mb-3">Варианты ответов</h6>

            <div id="answers-container">
                <?php
                $answers = $model->answers ?? [''];
                foreach ($answers as $i => $answer):
                    ?>
                    <div class="input-group mb-2 answer-row">
                    <span class="input-group-text">
                        <input type="checkbox"
                               name="StationForm[correct_answers][]"
                               value="<?= $i ?>"
                               <?= isset($model->correct_answers) && in_array($i, (array)$model->correct_answers) ? 'checked' : '' ?>
                        >
                    </span>

                        <input type="text"
                               class="form-control"
                               name="StationForm[answers][]"
                               value="<?= Html::encode($answer) ?>"
                               placeholder="Вариант ответа">

                        <button type="button" class="btn btn-outline-danger remove-answer">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add-answer" class="btn btn-sm btn-outline-primary mt-2">
                + Добавить вариант
            </button>

            <div class="form-text mt-2">
                Отметьте правильные варианты ответа
            </div>
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

<script>
    function toggleQuizBlock() {
        $('#quiz-block').toggle($('#station-type-select').val() === 'quiz');
    }

    toggleQuizBlock();
    $('#station-type-select').on('change', toggleQuizBlock);

    $('#add-answer').on('click', function () {
        const index = $('#answers-container .answer-row').length;

        $('#answers-container').append(`
        <div class="input-group mb-2 answer-row">
            <span class="input-group-text">
                <input type="checkbox" name="StationForm[correct_answers][]" value="\${index}">
            </span>
            <input type="text" class="form-control" name="StationForm[answers][]" placeholder="Вариант ответа">
            <button type="button" class="btn btn-outline-danger remove-answer">&times;</button>
        </div>
    `);
    });

    $(document).on('click', '.remove-answer', function () {
        $(this).closest('.answer-row').remove();
    });
</script>
