<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\QuestStationsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="quests-stations-search">

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
            <?= $form->field($model, 'quest_id') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'created_at') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'deleted_at') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
