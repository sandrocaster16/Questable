<?php

namespace app\controllers;

use app\models\Quests;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\QuestStations;
use app\models\QuestParticipants;
use app\models\StationProgress;
use app\core\services\QuestProgressService;

class GameController extends Controller
{
    public function actionPlay($qr)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['non-authorised', 'qr' => $qr]);
        }

        $station = QuestStations::find()
            ->where(['qr_identifier' => $qr])
            ->andWhere(['deleted_at' => null])
            ->one();

        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена. Возможно, QR-код устарел.');
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->user->returnUrl = ['game/play', 'qr' => $qr];
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
            if ($participant->save()) {
                $progressService = new QuestProgressService();
                $progressService->initializeProgress($participant);
            }
        }

        if ($participant->isBanned()) {
            throw new ForbiddenHttpException('Вы дисквалифицированы из этого квеста.');
        }

        $progressService = new QuestProgressService();
        $questProgress = $participant->getProgress();

        $progress = $participant->getStationProgress($station->id);
        if ($station->type === QuestStations::TYPE_INFO && (!$progress || !$progress->isStatusCompleted())) {
            $progressService->completeStation($participant, $station, 5);
            $progress = $participant->getStationProgress($station->id);
        }

        $nextStation = QuestStations::find()
            ->where(['quest_id' => $station->quest_id])
            ->andWhere(['>', 'id', $station->id])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        $isLastStation = $nextStation === null;

        return $this->render('play', [
            'station' => $station,
            'participant' => $participant,
            'progress' => $progress,
            'questProgress' => $questProgress,
            'isLastStation' => $isLastStation,
            'nextStation' => $nextStation,
        ]);
    }

    public function actionSubmitAnswer()
    {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return $this->goHome();
        }

        $stationId = $request->post('station_id');
        $userAnswerIndex = $request->post('answer');

        $station = QuestStations::findOne($stationId);
        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена');
        }

        $participant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $station->quest_id
        ]);

        if (!$participant || $participant->isBanned()) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        if ($participant->hasCompletedStation($station->id)) {
            return $this->redirect(['play', 'qr' => $station->qr_identifier]);
        }

        $options = json_decode($station->options, true);

        if (
            empty($options) ||
            !isset($options['answers'], $options['correct_answers']) ||
            !is_array($options['answers']) ||
            !is_array($options['correct_answers'])
        ) {
            Yii::$app->session->setFlash('error', 'Некорректная конфигурация вопроса');
            return $this->redirect(['play', 'qr' => $station->qr_identifier]);
        }

        $userAnswer = trim((string)$userAnswerIndex);

        $correctAnswers = [];
        foreach ($options['correct_answers'] as $index) {
            if (isset($options['answers'][$index])) {
                $correctAnswers[] = (string)$options['answers'][$index];
            }
        }

        $isCorrect = in_array($userAnswer, $correctAnswers, true);


        $progressService = new QuestProgressService();

        if ($isCorrect) {
            $progress = $progressService->completeStation($participant, $station, 10);

            if ($progress) {
                if ($participant->isQuestCompleted()) {
                    Yii::$app->session->setFlash(
                        'success',
                        'Верно! +10 баллов. Поздравляем! Квест завершен!'
                    );
                    return $this->redirect(['completion', 'quest_id' => $station->quest_id]);
                } else {
                    Yii::$app->session->setFlash('success', 'Верно! +10 баллов.');
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'Неверный ответ. Попробуйте ещё раз.');
        }

        return $this->redirect(['play', 'qr' => $station->qr_identifier]);
    }

    /**
     * Подтверждение прохождения станции куратором
     * @param int $station_id
     * @param int $participant_id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCuratorApprove($station_id, $participant_id)
    {
        $station = QuestStations::findOne($station_id);
        if (!$station) {
            throw new NotFoundHttpException('Станция не найдена');
        }

        $participant = QuestParticipants::findOne($participant_id);
        if (!$participant || $participant->quest_id !== $station->quest_id) {
            throw new NotFoundHttpException('Участник не найден');
        }

        $currentUserParticipant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $station->quest_id
        ]);

        if (!$currentUserParticipant || 
            !in_array($currentUserParticipant->role, [QuestParticipants::ROLE_OWNER, QuestParticipants::ROLE_VOLUNTEER])) {
            throw new ForbiddenHttpException('У вас нет прав для подтверждения прохождения станций');
        }

        if ($station->type !== QuestStations::TYPE_CURATOR_CHECK) {
            Yii::$app->session->setFlash('error', 'Эта станция не требует подтверждения куратором');
            return $this->redirect(Yii::$app->request->referrer ?: ['quest/index']);
        }

        $progressService = new QuestProgressService();
        $progress = $progressService->completeStation($participant, $station, 10);

        if ($progress) {
            if ($participant->isQuestCompleted()) {
                Yii::$app->session->setFlash('success', 'Прохождение станции подтверждено. +10 баллов. Поздравляем! Квест завершен!');
                return $this->redirect(['completion', 'quest_id' => $station->quest_id]);
            } else {
                Yii::$app->session->setFlash('success', 'Прохождение станции подтверждено. +10 баллов.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при подтверждении прохождения');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['quest/index']);
    }

    /**
     * Получить прогресс участника по квесту (API endpoint)
     * @param int $quest_id
     * @return \yii\web\Response
     */
    public function actionProgress($quest_id)
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Необходима авторизация');
        }

        $participant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $quest_id
        ]);

        if (!$participant) {
            throw new NotFoundHttpException('Участник не найден');
        }

        $progress = $participant->getProgress();

        if (Yii::$app->request->isAjax) {
            return $this->asJson($progress);
        }

        return $this->render('progress', [
            'progress' => $progress,
        ]);
    }

    /**
     * Страница завершения квеста
     * @param int $quest_id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCompletion($quest_id)
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Необходима авторизация');
        }

        $quest = Quests::findOne($quest_id);
        if (!$quest) {
            throw new NotFoundHttpException('Квест не найден');
        }

        $participant = QuestParticipants::findOne([
            'user_id' => Yii::$app->user->id,
            'quest_id' => $quest_id
        ]);

        if (!$participant) {
            throw new NotFoundHttpException('Участник не найден');
        }

        $progress = $participant->getProgress();

        if (!$progress['is_completed']) {
            return $this->redirect(['progress', 'quest_id' => $quest_id]);
        }

        return $this->render('completion', [
            'quest' => $quest,
            'participant' => $participant,
            'progress' => $progress,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionNonAuthorised($qr)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['play', 'qr' => $qr]);
        }

        $station = QuestStations::find()
            ->where(['qr_identifier' => $qr])
            ->andWhere(['deleted_at' => null])
            ->one();

        if ($station === null) {
            throw new NotFoundHttpException('Станция не найдена.');
        }

        $quest = Quests::find()
            ->where(['id' => $station->quest_id])
            ->andWhere(['delete_at' => null])
            ->one();

        if ($quest === null) {
            throw new NotFoundHttpException('Квест не найден.');
        }

        Yii::$app->user->returnUrl = ['game/play', 'qr' => $qr];

        return $this->render('non-authorised', [
            'quest'   => $quest,
            'station'=> $station,
        ]);
    }
}