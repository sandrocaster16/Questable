<?php

namespace app\controllers;

use app\core\services\DataProvider;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
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

        $logo_path = Url::to(Yii::getAlias('@upload/' . '2.jpeg'));
//        $logo_path = Yii::getAlias('@upload/' . Yii::$app->user->identity->id . '.jpeg');
        if (!file_exists($logo_path)) {
            $logo_path = Url::to(Yii::getAlias('@upload/users_avatars/default.jpeg'));
        }

        $avatar_path = Url::to(Yii::getAlias('@upload/' . 'users_avatars/' . 'avatar.jpeg'));

        $user_id = 2;
//        $user_id = Yii::$app->user->identity->id;
        $username = 'admin';
//        $username = Yii::$app->user->identity->login;

        return $this->render('index', [
            'user_history' => $dp->getLastQuestsByUserId(2),
            'popular_quests' => $dp->getPopularQuests(),
            'logo_path' => $logo_path,
            'avatar_path' => $avatar_path,
            'id' => $user_id,
            'username' => $username,
        ]);
    }

    public function actionInfo() {
        return $this->render('info');
    }
}
