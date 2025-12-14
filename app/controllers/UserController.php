<?php

namespace app\controllers;

use app\models\forms\ProfileForm;
use app\models\QuestParticipants;
use app\models\Quests;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\captcha\CaptchaAction;
use yii\web\ErrorAction;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        $user = Users::find()->where(['id' => Yii::$app->user->identity->id])->one();

        $model = new ProfileForm($user);

        if ($model->load(Yii::$app->request->post())) {
            $model->avatar = UploadedFile::getInstance($model, 'avatar');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Профиль успешно обновлен!');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении профиля.');
            }
        }

        $completedQuestCount = QuestParticipants::find()
            ->where(['user_id' => $user->id])
            ->count();

        $createdQuestCount = Quests::find()
            ->where(['creator_id' => $user->id])
            ->count();

        return $this->render('profile', [
            'model' => $model,
            'completedQuestCount' => $completedQuestCount,
            'createdQuestCount' => $createdQuestCount
        ]);
    }
}