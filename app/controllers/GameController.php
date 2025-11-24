<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\web\Controller;
use app\models\QuestStations;
use app\models\QuestParticipants;
use app\models\StationProgress;

class GameController extends Controller
{
    // Вход в игру через QR
    public function actionVisit($qr)
    {
        // Ищем станцию по QR и проверяем, что она не удалена
        $station = QuestStations::find()
            ->where(['qr_identifier' => $qr])
            ->andWhere(['deleted_at' => null])
            ->one();

        if (!$station) {
            throw new \yii\web\NotFoundHttpException('Станция не найдена или удалена.');
        }

        $userId = Yii::$app->user->id;
        if (!$userId) {
            // Редирект на логин с возвратом обратно (можно реализовать позже)
            return $this->redirect(['site/login']);
        }

        // Поиск или создание участника
        $participant = QuestParticipants::findOne([
            'user_id' => $userId,
            'quest_id' => $station->quest_id
        ]);

        if (!$participant) {
            $participant = new QuestParticipants();
            $participant->user_id = $userId;
            $participant->quest_id = $station->quest_id;
            $participant->role = 'player';
            $participant->points = 0; // Default 0
            $participant->created_at = date('Y-m-d H:i:s');
            // team_id пока null (одиночная игра)
            if (!$participant->save()) {
                throw new \yii\web\ServerErrorHttpException('Ошибка регистрации участника');
            }
        }

        // Проверка на бан в этом квесте (поле banned в quest_participants)
        if ($participant->banned !== null) {
            throw new \yii\web\ForbiddenHttpException('Вы забанены в этом квесте.');
        }

        // Проверяем прогресс по этой конкретной станции
        $progress = StationProgress::findOne([
            'participant_id' => $participant->id,
            'station_id' => $station->id
        ]);

        // Авто-завершение для типа 'info'
        if ($station->type === 'info' && !$progress) {
            $this->createProgress($participant->id, $station->id);
            // Рефреш прогресса для отображения
            $progress = StationProgress::findOne(['participant_id' => $participant->id, 'station_id' => $station->id]);
        }

        return $this->render('play', [
            'station' => $station,
            'participant' => $participant,
            'progress' => $progress
        ]);
    }

    // Обработка ответа на Quiz

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function actionSubmitAnswer()
    {
        $stationId = Yii::$app->request->post('station_id');
        $answer = Yii::$app->request->post('answer'); // То, что выбрал юзер

        $station = QuestStations::findOne($stationId);
        $participant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $station->quest_id
        ]);

        if (!$participant || $participant->banned) {
            return $this->goHome();
        }

        // Декодируем JSON options
        $options = json_decode($station->options, true, 512, JSON_THROW_ON_ERROR);
        $correctAnswer = $options['correct_answer'] ?? '';

        if ($answer === $correctAnswer) {
            // Верно -> создаем прогресс
            if (!$this->hasCompleted($participant->id, $station->id)) {
                $this->createProgress($participant->id, $station->id);

                // Начисляем очки (логика начисления очков может быть сложнее)
                $participant->points += 10;
                $participant->save(false);
            }
            Yii::$app->session->setFlash('success', 'Правильно!');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка. Попробуйте еще раз.');
        }

        return $this->redirect(['visit', 'qr' => $station->qr_identifier]);
    }

    // Хелпер создания записи прогресса
    private function createProgress($participantId, $stationId)
    {
        $progress = new StationProgress();
        $progress->participant_id = $participantId;
        $progress->station_id = $stationId;
        $progress->status = 'completed';
        $progress->completed_at = date('Y-m-d H:i:s');
        return $progress->save();
    }

    private function hasCompleted($participantId, $stationId)
    {
        return StationProgress::find()->where([
            'participant_id' => $participantId,
            'station_id' => $stationId,
            'status' => 'completed'
        ])->exists();
    }
}