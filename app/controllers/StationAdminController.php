<?php

namespace app\controllers;

use app\models\enum\SystemLogType;
use app\models\QuestParticipants;
use app\models\SystemLog;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StationAdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }
    public function actionClaim(string $token)
    {
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

        $participant = new QuestParticipants();
        $participant->user_id = $userId;
        $participant->quest_id = $data['quest_id'];
        $participant->role = QuestParticipants::ROLE_VOLUNTEER;
        $participant->points = 0;
        $participant->created_at = date('Y-m-d H:i:s');

        if (!$participant->save()) {
            throw new \RuntimeException('Ошибка назначения администратора');
        }

        $data['is_used'] = true;
        $log->message = json_encode($data, JSON_UNESCAPED_UNICODE);
        $log->save(false);

        Yii::$app->session->setFlash('success', 'Вы назначены администратором станции');

        return $this->redirect(['quest/view', 'id' => $data['quest_id']]);
    }
}
