<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\QuestsStations $model */

$this->title = 'Create Quests Stations';
$this->params['breadcrumbs'][] = ['label' => 'Quests Stations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-stations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
