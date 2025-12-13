<?php

namespace app\controllers;

use app\models\forms\ProfileForm;
use app\models\QuestParticipants;
use app\models\Quests;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

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

        $user = Users::find()->where(['id' => Yii::$app->user->identity->id])->one();

        $model = new ProfileForm($user);

        // Обработка отправки формы
        if ($model->load(Yii::$app->request->post())) {
            // Получаем файл
            $model->avatar = UploadedFile::getInstance($model, 'avatar');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Профиль успешно обновлен!');
                return $this->refresh(); // Перезагружаем страницу, чтобы сбросить POST данные
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
            'model' => $model, // Передаем модель в вид
            'completedQuestCount' => $completedQuestCount,
            'createdQuestCount' => $createdQuestCount
        ]);
    }
}