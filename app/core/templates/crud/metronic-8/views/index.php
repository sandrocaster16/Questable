<?php

use app\widgets\SVG;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

use app\widgets\SVG;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-block">
                <div class="d-none align-items-center position-relative my-1">
                    <?= "<?= " ?>SVG::get('icons/duotune/general/gen021.svg', 'svg-icon svg-icon-1 position-absolute ms-6') ?>
                    <input type="text" data-kt-customer-table-filter="search"
                           class="form-control form-control-solid w-250px ps-15" placeholder="Search Customers">
                </div>
                    <div class="d-none accordion accordion-icon-toggle" id="aboutThisPageAccordion">
                        <div class="mb-5">
                            <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse"
                                 data-bs-target="#aboutThisPageAccordionMain">
                            <span class="accordion-icon">
                                <?= "<?= " ?>SVG::get('icons/duotune/arrows/arr064.svg', 'svg-icon svg-icon-4') ?>
                            </span>
                                <h6 class="fs-7 text-muted fw-semibold mb-0 ms-4">О разделе</h6>
                            </div>
                            <div id="aboutThisPageAccordionMain" class="collapse fs-6 ps-10"
                                 data-bs-parent="#aboutThisPageAccordion">
                                В данном разделе возможно ...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('Добавить')?>, ['create'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">

        <?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>
            <div class="table-responsive-grid-wrapper">

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n<div class='mt-4 d-block d-lg-flex justify-content-between align-items-center text-center'>{summary}\n{pager}</div>",
        'options' => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer'],
        'captionOptions' => ['class' => 'text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0'],
        'pager' => [
        'registerLinkTags' => true,
        'linkOptions' => ['class' => 'page-link'],
        'prevPageLabel' => '<i class="previous"></i>',
        'nextPageLabel' => '<i class="next"></i>',
        'prevPageCssClass' => 'paginate_button page-item previous',
        'nextPageCssClass' => 'paginate_button page-item next',
        'pageCssClass' => 'paginate_button page-item',
                ],

                <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
            [
                'class' => ActionColumn::className(),
                'header' => 'Действия',
                'urlCreator' => function ($action, <?= $modelClass ?> $model, $key, $index, $column) {
                    return Url::toRoute([$action, <?= $generator->generateUrlParams() ?>]);
                 },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                'view' => function ($url, $model, $key) {
                return Html::a(SVG::get('icons/duotune/general/gen019.svg', 'svg-icon svg-icon-3'),
                $url, ['class' => 'btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 my-1']);
                },
                'update' => function ($url, $model, $key) {
                return Html::a(SVG::get('icons/duotune/art/art005.svg', 'svg-icon svg-icon-3'),
                $url, ['class' => 'btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 my-1']);
                },
                'delete' => function ($url, $model, $key) {
                return Html::a(SVG::get('icons/duotune/general/gen027.svg', 'svg-icon svg-icon-3'),
                $url, ['class' => 'btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 my-1']);
                },
                'menu' => function () {
                return '<a href="#" class="btn btn-bg-light btn-active-color-primary btn-sm me-1 my-1 menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Опции' .
                    SVG::get('icons/duotune/arrows/arr072.svg', 'svg-icon svg-icon-5 m-0') . '</a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true" data-popper-placement="bottom-end">
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">A</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">B</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">C</a>
                    </div>
                </div>
                <!--end::Menu-->';
                }
                ]
            ],
        ],
    ]); ?>
            </div>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $generator->getNameAttribute() ?>), ['view', <?= $generator->generateUrlParams() ?>]);
        },
    ]) ?>
<?php endif; ?>

<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>
        </div>
    </div>
</div>
