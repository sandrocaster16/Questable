<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\QuestsQuestions $model */

$this->title = 'Create Quests Questions';
$this->params['breadcrumbs'][] = ['label' => 'Quests Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-questions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
