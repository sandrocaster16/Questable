<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\QuestsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quests-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => ['data-pjax' => 1],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'id')->label('ID') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'name')->label('Название') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'tags')->label('Тэги') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'created_at')->label('Создан') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'deleted_at')->label('Удален') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::resetButton('Сбросить', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
