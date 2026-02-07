<?php
/** @var yii\web\View $this */
/** @var app\models\Quests $quest */
/** @var app\models\QuestStations $station */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Требуется авторизация';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body text-center">

                    <h2 class="mb-3">
                        <?= Html::encode($quest->name) ?>
                    </h2>

                    <h5 class="text-muted mb-4">
                        Станция: <?= Html::encode($station->name) ?>
                    </h5>

                    <p class="mb-4">
                        Для просмотра и прохождения этой станции необходимо войти в систему.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <?= Html::a(
                            'Войти',
                            ['/auth/login'],
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
