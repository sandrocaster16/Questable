<?php

namespace app\controllers;

use app\core\services\QuestProgressService;
use app\models\enum\SystemLogType;
use app\models\QuestParticipants;
use app\models\QuestStations;
use app\models\StationProgress;
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

    public function actionIndex()
    {
        $station = $this->getAdminStation();
        
        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена. Вы не назначены администратором станции.');
        }

        return $this->redirect(['station-admin/view', 'station_id' => $station->id]);
    }

    public function actionView($station_id)
    {
        $station = QuestStations::findOne($station_id);
        
        if (!$station || $station->deleted_at !== null) {
            throw new NotFoundHttpException('Станция не найдена');
        }

        // Проверяем, что текущий пользователь является администратором этой станции
        if (!$this->isAdminOfStation($station)) {
            throw new ForbiddenHttpException('У вас нет доступа к этой станции');
        }

        // Получаем список участников со статусом pending на этой станции
        $pendingParticipants = StationProgress::find()
            ->innerJoin('quest_participants', 'station_progress.participant_id = quest_participants.id')
            ->where([
                'station_progress.station_id' => $station->id,
                'station_progress.status' => StationProgress::STATUS_PENDING,
                'quest_participants.role' => QuestParticipants::ROLE_PLAYER,
            ])
            ->with(['participant.user', 'participant.team'])
            ->all();

        return $this->render('view', [
            'station' => $station,
            'pendingParticipants' => $pendingParticipants,
        ]);
    }

    public function actionApprove($progress_id)
    {
        if (!Yii::$app->request->isPost) {
            throw new ForbiddenHttpException('Разрешен только POST запрос');
        }

        $progress = StationProgress::find()->with(['station', 'participant'])->where(['id' => $progress_id])->one();
        
        if (!$progress) {
            throw new NotFoundHttpException('Прогресс не найден');
        }

        $station = $progress->station;
        $participant = $progress->participant;
        
        if (!$station || !$participant) {
            throw new NotFoundHttpException('Станция или участник не найдены');
        }

        if (!$this->isAdminOfStation($station)) {
            throw new ForbiddenHttpException('У вас нет доступа к этой станции');
        }

        if ($progress->isStatusCompleted()) {
            Yii::$app->session->setFlash('warning', 'Задание уже зачтено');
            return $this->redirect(['station-admin/view', 'station_id' => $station->id]);
        }

        $progressService = new QuestProgressService();
        $result = $progressService->completeStation($participant, $station, 10);

        if ($result) {
            Yii::$app->session->setFlash('success', 'Задание успешно зачтено');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при зачете задания');
        }

        return $this->redirect(['station-admin/view', 'station_id' => $station->id]);
    }

    public function actionReject($progress_id)
    {
        if (!Yii::$app->request->isPost) {
            throw new ForbiddenHttpException('Разрешен только POST запрос');
        }

        $progress = StationProgress::find()->with('station')->where(['id' => $progress_id])->one();
        
        if (!$progress) {
            throw new NotFoundHttpException('Прогресс не найден');
        }

        $station = $progress->station;
        
        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена');
        }
        
        if (!$this->isAdminOfStation($station)) {
            throw new ForbiddenHttpException('У вас нет доступа к этой станции');
        }

        // Удаляем запись о прогрессе (участник может попробовать снова)
        if ($progress->delete()) {
            Yii::$app->session->setFlash('success', 'Задание отклонено. Участник может попробовать снова.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при отклонении задания');
        }

        return $this->redirect(['station-admin/view', 'station_id' => $station->id]);
    }

    public function actionClaim(string $token)
    {
        // Проверяем авторизацию
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        $log = SystemLog::find()
            ->where(['type' => SystemLogType::StationAdminRegistration->value])
            ->andWhere(['like', 'message', '"token":"'.$token.'"'])
            ->one();

        if (!$log) {
            throw new NotFoundHttpException('Ссылка недействительна');
        }

        $data = json_decode($log->message, true);

        if (!isset($data['token']) || $data['token'] !== $token) {
            throw new NotFoundHttpException('Ссылка недействительна');
        }

        // Проверяем, был ли токен уже использован (ищем запись о регистрации администратора с этим токеном)
        $usedLog = SystemLog::find()
            ->where(['type' => SystemLogType::StationAdminRegistration->value])
            ->andWhere(['like', 'message', '"token":"'.$token.'"'])
            ->andWhere(['like', 'message', '"action":"admin_registered"'])
            ->one();

        if ($usedLog) {
            throw new ForbiddenHttpException('Ссылка уже использована');
        }

        $stationId = $data['station_id'] ?? null;
        $questId = $data['quest_id'] ?? null;
        $userId = Yii::$app->user->identity->id;

        if (!$stationId || !$questId) {
            throw new NotFoundHttpException('Некорректные данные ссылки');
        }

        // Проверяем существование станции
        $station = QuestStations::findOne($stationId);
        if (!$station || $station->deleted_at !== null) {
            throw new NotFoundHttpException('Станция не найдена');
        }

        // Проверяем, что пользователь еще не является администратором этой станции
        $existingParticipant = QuestParticipants::findOne([
            'user_id' => $userId,
            'quest_id' => $questId,
            'role' => QuestParticipants::ROLE_VOLUNTEER,
        ]);

        if ($existingParticipant) {
            // Проверяем, что это действительно для этой станции
            if ($this->isAdminOfStation($station)) {
                Yii::$app->session->setFlash('info', 'Вы уже являетесь администратором этой станции');
                return $this->redirect(['station-admin/view', 'station_id' => $stationId]);
            }
        }

        // Создаем запись участника с ролью volunteer
        $participant = new QuestParticipants();
        $participant->user_id = $userId;
        $participant->quest_id = $questId;
        $participant->role = QuestParticipants::ROLE_VOLUNTEER;
        $participant->points = 0;
        $participant->created_at = date('Y-m-d H:i:s');

        if (!$participant->save()) {
            throw new \RuntimeException('Ошибка назначения администратора: ' . implode(', ', $participant->getFirstErrors()));
        }

        // SystemLog нельзя обновлять (beforeSave возвращает false для update),
        // поэтому создаем новую запись в логе о том, что пользователь стал администратором
        // Это также служит отметкой о том, что токен был использован
        $adminLog = new SystemLog();
        $adminLog->type = SystemLogType::StationAdminRegistration->value;
        $adminLog->message = json_encode([
            'action' => 'admin_registered',
            'token' => $token,
            'user_id' => $userId,
            'username' => Yii::$app->user->identity->username,
            'station_id' => $stationId,
            'station_name' => $station->name,
            'quest_id' => $questId,
            'participant_id' => $participant->id,
            'registered_at' => date('Y-m-d H:i:s'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $adminLog->save(false);

        Yii::$app->session->setFlash('success', 'Вы успешно назначены администратором станции "' . $station->name . '"');

        return $this->redirect(['station-admin/view', 'station_id' => $stationId]);
    }

    /**
     * Получить станцию для текущего администратора
     * @return QuestStations|null
     */
    private function getAdminStation()
    {
        $userId = Yii::$app->user->identity->id;

        // Ищем записи о регистрации администраторов для текущего пользователя
        $logs = SystemLog::find()
            ->where(['type' => SystemLogType::StationAdminRegistration->value])
            ->andWhere(['like', 'message', '"action":"admin_registered"'])
            ->andWhere(['like', 'message', '"user_id":'.$userId])
            ->all();

        foreach ($logs as $log) {
            $data = json_decode($log->message, true);
            if (!$data || !isset($data['station_id']) || !isset($data['quest_id'])) {
                continue;
            }

            // Проверяем, существует ли участник с ролью volunteer для этого квеста и пользователя
            $participant = QuestParticipants::findOne([
                'user_id' => $userId,
                'quest_id' => $data['quest_id'],
                'role' => QuestParticipants::ROLE_VOLUNTEER,
            ]);

            if ($participant) {
                $station = QuestStations::findOne($data['station_id']);
                if ($station && $station->deleted_at === null) {
                    return $station;
                }
            }
        }

        return null;
    }

    /**
     * Проверить, является ли текущий пользователь администратором станции
     * @param QuestStations $station
     * @return bool
     */
    private function isAdminOfStation(QuestStations $station)
    {
        $userId = Yii::$app->user->identity->id;

        // Проверяем, является ли пользователь volunteer в этом квесте
        $participant = QuestParticipants::findOne([
            'user_id' => $userId,
            'quest_id' => $station->quest_id,
            'role' => QuestParticipants::ROLE_VOLUNTEER,
        ]);

        if (!$participant) {
            return false;
        }

        // Проверяем, что есть запись в SystemLog о назначении на эту станцию для текущего пользователя
        $logs = SystemLog::find()
            ->where(['type' => SystemLogType::StationAdminRegistration->value])
            ->andWhere(['like', 'message', '"action":"admin_registered"'])
            ->andWhere(['like', 'message', '"station_id":'.$station->id])
            ->andWhere(['like', 'message', '"user_id":'.$userId])
            ->all();

        foreach ($logs as $log) {
            $data = json_decode($log->message, true);
            if ($data && isset($data['station_id']) && $data['station_id'] == $station->id 
                && isset($data['quest_id']) && $data['quest_id'] == $station->quest_id
                && isset($data['user_id']) && $data['user_id'] == $userId) {
                return true;
            }
        }

        return false;
    }
}
