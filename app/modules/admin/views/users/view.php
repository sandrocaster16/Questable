<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            [
                'attribute' => 'role',
                'format' => 'raw',
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
                    return '<span class="' . $badgeClass . '">' . Html::encode($roleLabel) . '</span>' . 
                           ($model->id === 1 ? ' <span class="badge bg-danger">Защищен</span>' : '');
                },
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return $model->created_at ? date('d.m.Y H:i:s', strtotime($model->created_at)) : '-';
                },
            ],
            [
                'attribute' => 'deleted_at',
                'value' => function($model) {
                    return $model->deleted_at ? date('d.m.Y H:i:s', strtotime($model->deleted_at)) : '-';
                },
                'visible' => $model->deleted_at !== null,
            ],
            [
                'attribute' => 'banned',
                'label' => 'Banned',
                'value' => function($model) {
                    return $model->banned ? date('d.m.Y H:i:s', strtotime($model->banned)) : '-';
                },
                'visible' => $model->banned !== null,
            ],
        ],
    ]) ?>

</div>
