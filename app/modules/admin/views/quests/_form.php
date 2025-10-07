<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Quests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quests-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название') ?>

    <?= $form->field($model, 'tags')->checkboxList([
            //TODO
    ], [
            'itemOptions' => ['class' => 'form-check-input'],
            'separator' => '<br>',
    ])->label('Тэги') ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
