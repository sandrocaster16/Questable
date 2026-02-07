<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php
    // Получаем роли с русскими названиями
    $roleOptions = \app\models\Users::getRoleLabels();
    
    // Если это пользователь с id=1, ограничиваем выбор только админскими ролями
    $disabled = false;
    if ($model->id === 1) {
        $roleOptions = [
            \app\models\Users::ROLE_ROOT => 'Root (Супер-администратор)',
            \app\models\Users::ROLE_ADMIN => 'Администратор',
        ];
    }
    ?>

    <?= $form->field($model, 'role')->dropDownList($roleOptions, [
        'prompt' => 'Выберите роль',
        'disabled' => $model->id === 1,
    ])->hint($model->id === 1 ? 'Пользователь с ID=1 всегда должен быть администратором' : '') ?>

    <?php if (!$model->isNewRecord): ?>
        <div class="form-group">
            <label class="control-label">ID</label>
            <div class="form-control" style="background-color: #f8f9fa;">
                <?= Html::encode($model->id) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">Дата создания</label>
            <div class="form-control" style="background-color: #f8f9fa;">
                <?= $model->created_at ? date('d.m.Y H:i:s', strtotime($model->created_at)) : '-' ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($model->deleted_at): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Пользователь удален: <?= date('d.m.Y H:i:s', strtotime($model->deleted_at)) ?>
        </div>
    <?php endif; ?>

    <?php if ($model->banned): ?>
        <div class="alert alert-danger">
            <i class="fas fa-ban"></i> Пользователь забанен: <?= date('d.m.Y H:i:s', strtotime($model->banned)) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
