<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\models\Quests;
use app\models\QuestParticipants;
use app\models\QuestStations;
use app\core\services\QuestProgressService;

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

    /**
     * Просмотр квеста
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $quest = Quests::find()
            ->where(['id' => $id])
            ->andWhere(['delete_at' => null])
            ->one();

        if (!$quest) {
            throw new NotFoundHttpException('Квест не найден.');
        }

        // Получаем статистику квеста
        $stationsCount = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->count();

        $participantsCount = QuestParticipants::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['role' => QuestParticipants::ROLE_PLAYER])
            ->count();

        // Проверяем, участвует ли текущий пользователь в квесте
        $currentParticipant = null;
        $questProgress = null;
        if (!Yii::$app->user->isGuest) {
            $currentParticipant = QuestParticipants::findOne([
                'user_id' => Yii::$app->user->id,
                'quest_id' => $quest->id
            ]);

            if ($currentParticipant) {
                $progressService = new QuestProgressService();
                $questProgress = $progressService->getParticipantProgress($currentParticipant);
            }
        }

        return $this->render('quest-view', [
            'quest' => $quest,
            'stationsCount' => $stationsCount,
            'participantsCount' => $participantsCount,
            'currentParticipant' => $currentParticipant,
            'questProgress' => $questProgress,
        ]);
    }

    /**
     * Начать квест
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionStart($id)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('warning', 'Для начала квеста необходимо войти в систему.');
            Yii::$app->user->returnUrl = ['site/view', 'id' => $id];
            return $this->redirect(['auth/login']);
        }

        $quest = Quests::find()
            ->where(['id' => $id])
            ->andWhere(['delete_at' => null])
            ->one();

        if (!$quest) {
            throw new NotFoundHttpException('Квест не найден.');
        }

        $userId = Yii::$app->user->id;

        // Проверяем, не участвует ли уже пользователь в квесте
        $participant = QuestParticipants::findOne([
            'user_id' => $userId,
            'quest_id' => $quest->id
        ]);

        if ($participant) {
            if ($participant->isBanned()) {
                Yii::$app->session->setFlash('error', 'Вы дисквалифицированы из этого квеста.');
                return $this->redirect(['site/view', 'id' => $id]);
            }

            // Если уже участвует, перенаправляем на прогресс или первую станцию
            $progressService = new QuestProgressService();
            $nextStation = $progressService->getNextAvailableStation($participant);

            if ($nextStation) {
                return $this->redirect(['game/visit', 'qr' => $nextStation->qr_identifier]);
            } else {
                // Все станции пройдены, показываем прогресс
                return $this->redirect(['game/progress', 'quest_id' => $quest->id]);
            }
        }

        // Создаем нового участника
        $participant = new QuestParticipants();
        $participant->user_id = $userId;
        $participant->quest_id = $quest->id;
        $participant->role = QuestParticipants::ROLE_PLAYER;
        $participant->points = 0;
        $participant->created_at = date('Y-m-d H:i:s');

        if ($participant->save()) {
            // Инициализируем прогресс для нового участника
            $progressService = new QuestProgressService();
            $progressService->initializeProgress($participant);

            // Получаем первую станцию
            $firstStation = QuestStations::find()
                ->where(['quest_id' => $quest->id])
                ->andWhere(['deleted_at' => null])
                ->orderBy(['id' => SORT_ASC])
                ->one();

            if ($firstStation) {
                Yii::$app->session->setFlash('success', 'Квест начат! Удачи!');
                return $this->redirect(['game/visit', 'qr' => $firstStation->qr_identifier]);
            } else {
                Yii::$app->session->setFlash('warning', 'Квест начат, но в нем пока нет станций.');
                return $this->redirect(['site/view', 'id' => $id]);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при начале квеста.');
            return $this->redirect(['site/view', 'id' => $id]);
        }
    }
}