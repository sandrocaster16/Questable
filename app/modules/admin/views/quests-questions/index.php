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
$this->registerCssFile('@web/css/admin-enhancements.css');
?>
<div class="quests-questions-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-question-circle text-primary"></i> <?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="fas fa-plus"></i> Создать вопрос', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="alert alert-light border-start border-primary border-4 mb-4">
        <i class="fas fa-info-circle text-primary"></i> 
        <strong>Управление вопросами:</strong> Создавайте и редактируйте вопросы для станций квестов.
    </div>

    <?php Pjax::begin(); ?>

    <div class="card mb-4">
        <div class="card-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'yii\widgets\LinkPager',
            'options' => ['class' => 'pagination justify-content-center'],
            'linkOptions' => ['class' => 'page-link'],
            'activePageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
            'prevPageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'firstPageCssClass' => 'page-item',
            'lastPageCssClass' => 'page-item',
        ],
        'layout' => "{summary}\n<div class='table-responsive'>{items}</div>\n{pager}",
        'summary' => '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> вопросов</div>',
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
    ]); ?>

    <?php Pjax::end(); ?>


</div>
