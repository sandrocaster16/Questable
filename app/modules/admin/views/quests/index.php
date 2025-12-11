<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\QuestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Квесты';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-enhancements.css');
?>
<div class="quests-index container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-map-marked-alt text-success"></i> <?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="fas fa-plus"></i> Создать квест', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="alert alert-light border-start border-success border-4 mb-4">
        <i class="fas fa-info-circle text-success"></i> 
        <strong>Управление квестами:</strong> Создавайте и редактируйте квесты, управляйте станциями и отслеживайте статистику.
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
            'summary' => '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> квестов</div>',
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
