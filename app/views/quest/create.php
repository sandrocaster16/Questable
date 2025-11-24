<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\forms\QuestForm */

$this->title = 'Создание квеста';
?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Создание нового квеста</h2>
        <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-secondary">Назад</a>
    </div>

    <div class="quest-form-container" style="max-width: 600px; margin: 0 auto;">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="form-group">
            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Название квеста (например: Тайны парка)'])->label('Название') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'description')->textarea(['rows' => 4, 'placeholder' => 'О чем этот квест?'])->label('Описание') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'coverFile')->fileInput()->label('Обложка (необязательно)') ?>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <?= Html::submitButton('Создать квест', ['class' => 'btn btn-primary', 'style' => 'width: 100%']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>