<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model app\models\forms\QuestForm */

$this->title = 'Создание квеста';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Новый квест</h2>
        <a href="<?= Url::to(['index']) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Назад
        </a>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Шаг 1: Основные настройки</h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['class' => 'form-control', 'placeholder' => 'Например: Тайны старого парка'])->label('Название') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'coverFile')->fileInput(['class' => 'form-control'])->label('Обложка') ?>
                </div>
            </div>

            <?= $form->field($model, 'description')->textarea(['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Описание для игроков'])->label('Описание') ?>

            <div class="mt-4 text-end">
                <?= Html::submitButton('Создать и перейти к станциям <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success px-4']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="card shadow-sm opacity-50" style="pointer-events: none;">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0">Шаг 2: Станции (Точки маршрута)</h5>
            <button class="btn btn-secondary btn-sm disabled"><i class="fas fa-plus"></i> Добавить станцию</button>
        </div>
        <div class="card-body text-center py-4">
            <p class="text-muted mb-0">Сначала сохраните квест, чтобы добавлять точки маршрута.</p>
        </div>
    </div>
</div>