<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\Quests;
use app\models\QuestParticipants;

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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        $user = Yii::$app->user->identity;
        $userId = $user->id;

        $uploadPath = Yii::getAlias('@webroot/uploads/users_avatars/');
        $uploadUrl = Yii::getAlias('@web/uploads/users_avatars/');
        $uploadUrlLogo = Yii::getAlias('@web/uploads/');

        if (!empty($user->avatar_url)) {
            if (strpos($user->avatar_url, 'http') === 0) {
                $avatar_path = $user->avatar_url;
            } else {
                $avatar_path = $user->avatar_url;
            }
        } else {
            if (file_exists($uploadPath . 'avatar.jpeg')) {
                $avatar_path = $uploadUrl . 'avatar.jpeg';
            } else {
                $avatar_path = $uploadUrl . 'default.png';
            }
        }

        $logoFilename = $userId . '.jpeg';
        if (file_exists($uploadPath . $logoFilename)) {
            $logoUrl = $uploadUrlLogo . $logoFilename;
        } else {
            $logoUrl = $uploadUrlLogo . 'default_logo.jpeg';
        }

        $historyQuery = QuestParticipants::find()
            ->where(['user_id' => $userId])
            ->with('quest')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        $user_history = [];
        foreach ($historyQuery as $participant) {
            if ($participant->quest) {
                $questArr = $participant->quest->toArray();
                $questArr['participation_date'] = $participant->created_at;
                $user_history[] = $questArr;
            }
        }

        $popular_quests = Quests::find()
            ->where(['delete_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        return $this->render('index', [
            'user_history' => $user_history,
            'popular_quests' => $popular_quests,
            'avatar_path' => $avatar_path,
            'id' => $userId,
            'username' => $user->username,
        ]);
    }

    public function actionInfo() {
        return $this->render('info');
    }
}