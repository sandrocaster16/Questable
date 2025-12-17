<?php

namespace app\controllers;

use Yii;
use yii\captcha\CaptchaAction;
use yii\web\Controller;
use yii\web\ErrorAction;

class StationAdminController extends Controller
{
    public function actionClaim(string $token)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->returnUrl = Yii::$app->request->url;
            return $this->redirect(['site/login']);
        }

        $log = SystemLog::find()
            ->where(['type' => SystemLogType::StationAdminRegistration->value])
            ->andWhere(['like', 'message', '"token":"'.$token.'"'])
            ->one();

        if (!$log) {
            throw new NotFoundHttpException('Ссылка недействительна');
        }

        $data = json_decode($log->message, true);

        if ($data['is_used']) {
            throw new ForbiddenHttpException('Ссылка уже использована');
        }

        $stationId = $data['station_id'];
        $userId = Yii::$app->user->id;

        /** 1. Проверка: пользователь уже админ другой станции */
        $exists = QuestParticipants::find()
            ->where([
                'user_id' => $userId,
                'role' => QuestParticipants::ROLE_VOLUNTEER,
            ])
            ->exists();

        if ($exists) {
            throw new ForbiddenHttpException('Вы уже являетесь администратором станции');
        }

        /** 2. Проверка: у станции уже есть админ */
        $stationAdminExists = QuestParticipants::find()
            ->where([
                'quest_id' => $data['quest_id'],
                'role' => QuestParticipants::ROLE_VOLUNTEER,
            ])
            ->exists();

        if ($stationAdminExists) {
            throw new ForbiddenHttpException('У станции уже есть администратор');
        }

        /** 3. Назначаем админа */
        $participant = new QuestParticipants();
        $participant->user_id = $userId;
        $participant->quest_id = $data['quest_id'];
        $participant->role = QuestParticipants::ROLE_VOLUNTEER;
        $participant->points = 0;
        $participant->created_at = date('Y-m-d H:i:s');

        if (!$participant->save()) {
            throw new \RuntimeException('Ошибка назначения администратора');
        }

        /** 4. Помечаем ссылку использованной */
        $data['is_used'] = true;
        $log->message = json_encode($data, JSON_UNESCAPED_UNICODE);
        $log->save(false);

        Yii::$app->session->setFlash('success', 'Вы назначены администратором станции');

        return $this->redirect(['quest/view', 'id' => $data['quest_id']]);
    }
}
