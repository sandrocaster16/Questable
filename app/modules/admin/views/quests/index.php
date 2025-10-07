<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\QuestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Квесты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-index container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Создать квест', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::begin(); ?>
    <div class="card mb-4">
        <div class="card-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'name',
//                    'tags',                                                        TODO json decode
                    [
                            'attribute' => 'created_at',
                            'format' => ['date', 'php:Y-m-d H:i'],
                    ],
                    [
                            'attribute' => 'deleted_at',
                            'format' => ['date', 'php:Y-m-d H:i'],
                    ],

                    [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Actions',
                            'contentOptions' => ['class' => 'text-center'],
                    ],
            ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
