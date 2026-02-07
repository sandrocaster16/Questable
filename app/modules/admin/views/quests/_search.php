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
            'options' => [
                    'data-pjax' => 1 // Если используете Pjax
            ],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'id') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'name') ?>
        </div>

        <div class="col-md-3">
            <!-- Исправлено с deleted_at на delete_at, как в модели -->
            <?= $form->field($model, 'delete_at')->textInput(['placeholder' => 'ГГГГ-ММ-ДД']) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'created_at')->textInput(['placeholder' => 'ГГГГ-ММ-ДД']) ?>
        </div>
    </div>

    <!-- Поле tags удалено, так как его нет в базе данных и модели -->

    <div class="form-group mt-2">
        <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>