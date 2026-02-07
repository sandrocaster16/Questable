<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model app\models\forms\QuestForm */

$this->title = 'Questable - Создание квеста';
?>

<div class="container py-4 quest-create">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Новый квест</h2>
        <a href="<?= Url::to(['index']) ?>" class="btn-nice btn-nice-secondary">
            <i class="fas fa-arrow-left"></i> Назад
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Шаг 1: Основные настройки</h5>
        </div>

        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')
                        ->textInput(['placeholder' => 'Например: Тайны старого парка'])
                        ->label('Название') ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'coverFile')
                        ->fileInput()
                        ->label('Обложка') ?>
                </div>
            </div>

            <?= $form->field($model, 'description')
                ->textarea(['rows' => 3, 'placeholder' => 'Описание для игроков'])
                ->label('Описание') ?>

            <div class="mt-4 justify-content-end d-flex">
                <?= Html::submitButton(
                    'Создать и перейти к станциям <i class="fas fa-arrow-right"></i>',
                    ['class' => 'btn-nice btn-nice-primary']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="card opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Шаг 2: Станции (Точки маршрута)</h5>
            <button class="btn-nice btn-nice-secondary btn-sm" disabled>
                <i class="fas fa-plus"></i> Добавить станцию
            </button>
        </div>

        <div class="card-body text-center py-4">
            <p class="text-muted mb-0">
                Сначала сохраните квест, чтобы добавлять точки маршрута.
            </p>
        </div>
    </div>

</div>
