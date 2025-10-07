<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Quests $model */

$this->title = 'Create Quests';
$this->params['breadcrumbs'][] = ['label' => 'Quests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
