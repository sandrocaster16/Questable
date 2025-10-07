<?php

use app\models\QuestsQuestions;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\QuestsQuestionsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Quests Questions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quests-questions-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Quests Questions', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <div class="card mb-4">
        <div class="card-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'station_id',
            'question',
            'answer:ntext',
            'help',
            'message',
            'created_at',
            'deleted_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuestsQuestions $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
            'pager' => [
                'class' => LinkPager::class,
                'options' => ['class' => 'pagination pagination-sm justify-content-center'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link', 'data-pjax' => 1],
                'firstPageLabel' => '«',
                'prevPageLabel'  => '‹',
                'nextPageLabel'  => '›',
                'lastPageLabel'  => '»',
                'maxButtonCount' => 8,
                'hideOnSinglePage' => true,
            ],
    ]); ?>

    <?php Pjax::end(); ?>


</div>
