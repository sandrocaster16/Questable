<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
<?php if ($generator->enablePjax): ?>
        'options' => [
            'data-pjax' => 1
        ],
<?php endif; ?>
    ]); ?>
    <div class="row">

        <div class="col-12 col-lg-4">
            <div class="mb-2">
    <?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    } else {
        echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    }
}
?>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="mb-2">
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="mb-2">
            </div>
        </div>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Найти') ?>, ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(<?= $generator->generateString('Сброс') ?>, ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
