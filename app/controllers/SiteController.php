<?php

namespace app\controllers;

use app\core\services\DataProvider;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
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

    public function actionIndex()
    {
        $dp = new DataProvider();

        $logo_path = '\app\pictures\users_avatars\ . Yii::$app->user->identity->tg_id';
        if (!file_exists($logo_path)) {
            $logo_path = '\app\pictures\users_avatars\default.png';
        }

        return $this->render('index', [
//            'last_quests' => $dp->getLastQuestsByUserId(Yii::$app->user->identity->id),
//            'popular_quests' => $dp->getPopularQuests(),
//            'logo_path' => $logo_path
        ]);
    }

    public function actionInfo() {
        return $this->render('info');
    }
}
