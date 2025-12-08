<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\QuestStations;
use app\models\QuestParticipants;
use app\models\StationProgress;

class GameController extends Controller
{
    public function actionVisit($qr)
    {
        $station = QuestStations::find()
            ->where(['qr_identifier' => $qr])
            ->andWhere(['deleted_at' => null])
            ->one();

        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена. Возможно, QR-код устарел.');
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->user->returnUrl = ['game/visit', 'qr' => $qr];
            return $this->redirect(['site/login']);
        }

        $userId = Yii::$app->user->id;

        $participant = QuestParticipants::findOne(['user_id' => $userId, 'quest_id' => $station->quest_id]);
        if (!$participant) {
            $participant = new QuestParticipants();
            $participant->user_id = $userId;
            $participant->quest_id = $station->quest_id;
            $participant->role = 'player';
            $participant->points = 0;
            $participant->created_at = date('Y-m-d H:i:s');
            $participant->save();
        }

        if ($participant->banned) {
            throw new ForbiddenHttpException('Вы дисквалифицированы из этого квеста.');
        }

        $progress = StationProgress::findOne([
            'participant_id' => $participant->id,
            'station_id' => $station->id
        ]);

        if ($station->type === 'info' && !$progress) {
            $this->createProgress($participant, $station, 5);
            $progress = StationProgress::findOne(['participant_id' => $participant->id, 'station_id' => $station->id]);
        }

        return $this->render('play', [
            'station' => $station,
            'participant' => $participant,
            'progress' => $progress
        ]);
    }

    public function actionSubmitAnswer()
    {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return $this->goHome();
        }

        $stationId = $request->post('station_id');
        $userAnswer = trim($request->post('answer'));

        $station = QuestStations::findOne($stationId);
        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена');
        }

        $participant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $station->quest_id
        ]);

        if (!$participant || $participant->banned) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        $existingProgress = StationProgress::findOne([
            'participant_id' => $participant->id,
            'station_id' => $station->id,
            'status' => 'completed'
        ]);

        if ($existingProgress) {
            Yii::$app->session->setFlash('info', 'Вы уже ответили на этот вопрос.');
            return $this->redirect(['visit', 'qr' => $station->qr_identifier]);
        }

        $options = json_decode($station->options, true);
        $correctAnswer = $options['correct_answer'] ?? '';

        if ($userAnswer === $correctAnswer) {

            $progress = new StationProgress();
            $progress->participant_id = $participant->id;
            $progress->station_id = $station->id;
            $progress->status = 'completed';
            $progress->completed_at = date('Y-m-d H:i:s');

            if ($progress->save()) {
                $participant->updateCounters(['points' => 10]);

                Yii::$app->session->setFlash('success', 'Верно! +10 баллов.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка! Попробуйте еще раз.');
        }

        return $this->redirect(['visit', 'qr' => $station->qr_identifier]);
    }
    public function actionCuratorApprove($station_id, $participant_id)
    {
    }

    private function createProgress($participant, $station, $pointsToAdd = 0)
    {
        $progress = new StationProgress();
        $progress->participant_id = $participant->id;
        $progress->station_id = $station->id;
        $progress->status = 'completed';
        $progress->completed_at = date('Y-m-d H:i:s');

        if ($progress->save()) {
            if ($pointsToAdd > 0) {
                $participant->points += $pointsToAdd;
                $participant->save(false);
            }
        }
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