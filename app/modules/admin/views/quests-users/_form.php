<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\QuestsUsers $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="quests-users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'quest_id')->textInput() ?>

    <?= $form->field($model, 'role')->dropDownList([ 'player' => 'Player', 'owner' => 'Owner', 'volunteer' => 'Volunteer', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'command_id')->textInput() ?>

    <?= $form->field($model, 'points')->textInput() ?>

    <?= $form->field($model, 'banned')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
