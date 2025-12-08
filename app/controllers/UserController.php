<?php

namespace app\controllers;

use app\models\QuestParticipants;
use app\models\Quests;
use Yii;
use yii\web\Controller;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        $completedQuestCount = QuestParticipants::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->count();

        $createdQuestCount = Quests::find()
            ->where(['creator_id' => Yii::$app->user->id])
            ->count();

        return $this->render('profile', [
            'completedQuestCount' => $completedQuestCount,
            'createdQuestCount' => $createdQuestCount
        ]);
    }
}