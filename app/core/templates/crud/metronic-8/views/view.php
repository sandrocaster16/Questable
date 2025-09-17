<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="card card-flush pt-3 mb-5 mb-xl-10">
        <div class="card-header">
            <div class="card-title">
                <h2 class="fw-bold"><?= "<?= " ?>Html::encode($this->title) ?></h2>
            </div>
            <div class="card-toolbar">
                <?= "<?= " ?>Html::a('Изменить', ['update', <?= $urlParams ?>], ['class' => 'btn btn-light-primary me-2']) ?>
                <?= "<?= " ?>Html::a('Удалить', ['delete', <?= $urlParams ?>], [
                'class' => 'btn btn-light-danger',
                'data' => [
                'confirm' => 'Уверены что хотите удалить этот элемент?',
                'method' => 'post',
                ],
                ]) ?>
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="mb-10">
                <h5 class="mb-4">Основная информация</h5>
                <div class="d-flex flex-wrap py-5">
                    <div class="flex-equal me-5">
                      <?= "<?= " ?>DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0'],
                        'template' => '<tr><td class="text-gray-400 min-w-175px w-175px" {captionOptions}>{label}</td><td class="text-gray-800 min-w-200px" {contentOptions}>{value}</td></tr>',
                        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
        ],
    ]) ?>
                    </div>
                        <div class="d-none flex-equal">
                            <?= "<?= " ?>DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0'],
                            'template' => '<tr><td class="text-gray-400 min-w-175px w-175px" {captionOptions}>{label}</td><td class="text-gray-800 min-w-200px" {contentOptions}>{value}</td></tr>',
                            'attributes' => []
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="d-none mb-0">
                <h5 class="mb-4">Дополнительная информация</h5>
            </div>
        </div>
    </div>
</div>