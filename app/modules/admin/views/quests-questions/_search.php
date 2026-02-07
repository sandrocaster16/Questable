<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\QuestsQuestionsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="quests-questions-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => ['data-pjax' => 1],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'station_id') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'question') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'answer') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'help') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
