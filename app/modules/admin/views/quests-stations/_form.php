<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\QuestStations $model */
/** @var yii\widgets\ActiveForm $form */

$formattedOptions = $model->options;
if (!empty($model->options)) {
    try {
        $decoded = json_decode($model->options, true, 512, JSON_THROW_ON_ERROR);
        $formattedOptions = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        $formattedOptions = $model->options;
    }
}
?>

<div class="quests-stations-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'quest_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'deleted_at')->textInput() ?>

    <?= $form->field($model, 'options')->textarea([
            'rows' => 10,
            'value' => $formattedOptions,
            'placeholder' => "{\n  \"key\": \"value\"\n}",
            'style' => 'font-family: monospace;',
    ])?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
