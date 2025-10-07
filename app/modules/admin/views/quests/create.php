<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quests */

$this->title = 'Создать квест';
$this->params['breadcrumbs'][] = ['label' => 'Квесты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
    ]) ?>

</div>