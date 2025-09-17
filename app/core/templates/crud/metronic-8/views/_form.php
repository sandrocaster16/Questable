<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="adminpage-form <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-12 col-lg-4">
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo '<div class="fv-row mb-2">' . "\n";
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
        echo '</div>' . "\n";
    }
} ?>
        </div>
        <div class="col-12 col-lg-4">
            <div class="mb-2">
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="mb-2">
            </div>
        </div>
    </div>
    <div class="row adminpage-form-footer">
        <div class="col-12">
            <div class="form-group mt-4">
             <?= "<?= " ?>Html::submitButton('Сохранить', ['id' => 'savePageButton','class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
