<?php

use app\models\QuestsUsers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\QuestsUsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Quests Users';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-enhancements.css');
?>
<div class="quests-users-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-user-friends text-warning"></i> <?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="fas fa-plus"></i> Создать участника', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="alert alert-light border-start border-warning border-4 mb-4">
        <i class="fas fa-info-circle text-warning"></i> 
        <strong>Управление участниками:</strong> Просматривайте и управляйте участниками квестов.
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
        'summary' => '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> участников</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'quest_id',
            'role',
            'command_id',
            //'points',
            //'banned',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuestsUsers $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
