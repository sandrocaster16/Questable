<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\QuestsQuestions $model */

$this->title = 'Update Quests Questions: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quests Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quests-questions-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
