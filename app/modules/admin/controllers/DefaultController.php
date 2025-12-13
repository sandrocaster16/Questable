<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Users;
use app\models\Quests;
use app\models\QuestStations;
use app\models\QuestParticipants;
use app\models\QuestTeams;
use app\core\services\QuestProgressService;

/**
 * DefaultController для главной страницы админ-панели
 */
class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->id === 1 || $user->isAdmin());
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Главная страница админ-панели
     * @return string
     */
    public function actionIndex()
    {
        // Статистика пользователей
        $totalUsers = Users::find()->where(['deleted_at' => null])->count();
        $activeUsers = Users::find()
            ->where(['deleted_at' => null])
            ->andWhere(['banned' => null])
            ->count();
        $bannedUsers = Users::find()->where(['!=', 'banned', null])->count();
        $adminUsers = Users::find()
            ->where(['deleted_at' => null])
            ->andWhere(['in', 'role', [Users::ROLE_ADMIN, Users::ROLE_ROOT]])
            ->count();
        $volunteerUsers = Users::find()
            ->where(['deleted_at' => null])
            ->andWhere(['role' => Users::ROLE_VOLUNTEER])
            ->count();

        // Статистика квестов
        $totalQuests = Quests::find()->where(['delete_at' => null])->count();
        $activeQuests = Quests::find()
            ->where(['delete_at' => null])
            ->count();
        $totalStations = QuestStations::find()
            ->where(['deleted_at' => null])
            ->count();

        // Статистика участников
        $totalParticipants = QuestParticipants::find()
            ->where(['role' => QuestParticipants::ROLE_PLAYER])
            ->count();
        $activeParticipants = QuestParticipants::find()
            ->where(['role' => QuestParticipants::ROLE_PLAYER])
            ->andWhere(['banned' => null])
            ->count();
        $bannedParticipants = QuestParticipants::find()
            ->where(['!=', 'banned', null])
            ->count();

        // Статистика команд
        $totalTeams = QuestTeams::find()->count();

        // Статистика прогресса
        $progressService = new QuestProgressService();
        $completedQuests = 0;
        $questsWithProgress = Quests::find()
            ->where(['delete_at' => null])
            ->limit(10)
            ->all();

        foreach ($questsWithProgress as $quest) {
            $stats = $progressService->getQuestStatistics($quest);
            if ($stats['completed_quest'] > 0) {
                $completedQuests++;
            }
        }

        // Последние пользователи
        $recentUsers = Users::find()
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // Последние квесты
        $recentQuests = Quests::find()
            ->where(['delete_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // Топ квесты по участникам
        $topQuestsData = Quests::find()
            ->alias('q')
            ->select(['q.id', 'participants_count' => 'COUNT(qp.id)'])
            ->leftJoin('quest_participants qp', 'q.id = qp.quest_id AND qp.role = :role', [':role' => 'player'])
            ->where(['q.delete_at' => null])
            ->groupBy('q.id')
            ->orderBy(['participants_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        $topQuestsByParticipants = [];
        foreach ($topQuestsData as $data) {
            $quest = Quests::findOne($data['id']);
            if ($quest) {
//                $quest->participants_count = (int)$data['participants_count'];
                $topQuestsByParticipants[] = $quest;
            }
        }

        return $this->render('index', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'bannedUsers' => $bannedUsers,
            'adminUsers' => $adminUsers,
            'volunteerUsers' => $volunteerUsers,
            'totalQuests' => $totalQuests,
            'activeQuests' => $activeQuests,
            'totalStations' => $totalStations,
            'totalParticipants' => $totalParticipants,
            'activeParticipants' => $activeParticipants,
            'bannedParticipants' => $bannedParticipants,
            'totalTeams' => $totalTeams,
            'completedQuests' => $completedQuests,
            'recentUsers' => $recentUsers,
            'recentQuests' => $recentQuests,
            'topQuestsByParticipants' => $topQuestsByParticipants,
        ]);
    }
}

