<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('Создать ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
    <div class="card card-flush">
        <div class="card-header d-none card-header pt-3 mb-5 mb-xl-10">
            <div class="card-title">
                <h2 class="fw-bold"><?= "<?= " ?>Html::encode($this->title) ?></h2>
            </div>
            <div class="card-toolbar">
            </div>
        </div>
        <div class="card-body pt-5">
            <div class="mb-4">
                <?= "<?= " ?>$this->render('_form', [
                'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
