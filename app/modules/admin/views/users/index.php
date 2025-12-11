<?php

use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\UsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/admin-enhancements.css');
?>
<div class="users-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-users text-primary"></i> <?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="fas fa-plus"></i> Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
        'summary' => '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> пользователей</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            [
                'attribute' => 'role',
                'value' => function($model) {
                    $labels = \app\models\Users::getRoleLabels();
                    $roleLabel = $labels[$model->role] ?? $model->role;
                    $badgeClass = match($model->role) {
                        'root' => 'badge bg-danger',
                        'admin' => 'badge bg-warning text-dark',
                        'volunteer' => 'badge bg-info',
                        'user' => 'badge bg-secondary',
                        default => 'badge bg-secondary'
                    };
                    $html = '<span class="' . $badgeClass . '">' . Html::encode($roleLabel) . '</span>';
                    if ($model->id === 1) {
                        $html .= ' <span class="badge bg-danger" title="Пользователь с ID=1 всегда администратор">Защищен</span>';
                    }
                    return $html;
                },
                'format' => 'raw',
                'filter' => \app\models\Users::getRoleLabels(),
                'contentOptions' => ['style' => 'min-width: 200px;'],
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return $model->created_at ? date('d.m.Y H:i', strtotime($model->created_at)) : '-';
                },
            ],
            [
                'attribute' => 'deleted_at',
                'value' => function($model) {
                    return $model->deleted_at ? '<span class="badge bg-danger">Удален</span>' : '<span class="badge bg-success">Активен</span>';
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Быстрое изменение роли',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->id === 1) {
                        return '<span class="text-muted">Недоступно</span>';
                    }
                    $currentRole = $model->role;
                    $availableRoles = \app\models\Users::getRoleLabels();
                    
                    $buttons = [];
                    foreach ($availableRoles as $role => $label) {
                        if ($role !== $currentRole) {
                            $badgeClass = match($role) {
                                'root' => 'badge bg-danger',
                                'admin' => 'badge bg-warning text-dark',
                                'volunteer' => 'badge bg-info',
                                'user' => 'badge bg-secondary',
                                default => 'badge bg-secondary'
                            };
                            $buttons[] = Html::a(
                                '<span class="' . $badgeClass . '">' . Html::encode($label) . '</span>',
                                ['change-role', 'id' => $model->id, 'role' => $role],
                                [
                                    'data-confirm' => 'Вы уверены, что хотите изменить роль пользователя на "' . $label . '"?',
                                    'title' => 'Изменить на ' . $label,
                                    'style' => 'margin-right: 5px;',
                                ]
                            );
                        }
                    }
                    return !empty($buttons) ? implode(' ', $buttons) : '<span class="text-muted">Все роли применены</span>';
                },
                'contentOptions' => ['style' => 'min-width: 350px;'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'urlCreator' => function ($action, Users $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
