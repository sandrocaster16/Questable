<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\QuestsUsers $model */

$this->title = 'Create Quests Users';
$this->params['breadcrumbs'][] = ['label' => 'Quests Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
