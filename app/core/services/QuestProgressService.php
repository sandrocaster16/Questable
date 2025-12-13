<?php

namespace app\core\services;

use app\models\QuestParticipants;
use app\models\QuestStations;
use app\models\Quests;
use app\models\StationProgress;
use app\models\QuestTeams;
use yii\db\Query;

/**
 * Сервис для отслеживания прогресса прохождения квеста
 */
class QuestProgressService
{
    /**
     * Получить прогресс участника по квесту
     *
     * @param QuestParticipants $participant
     * @return array
     */
    public function getParticipantProgress(QuestParticipants $participant): array
    {
        $quest = $participant->quest;
        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $totalStations = count($stations);
        $completedStations = StationProgress::find()
            ->where(['participant_id' => $participant->id])
            ->andWhere(['status' => StationProgress::STATUS_COMPLETED])
            ->count();

        $progressPercentage = $totalStations > 0 
            ? round(($completedStations / $totalStations) * 100, 2) 
            : 0;

        $stationsProgress = [];
        foreach ($stations as $station) {
            $progress = StationProgress::findOne([
                'participant_id' => $participant->id,
                'station_id' => $station->id
            ]);

            $stationsProgress[] = [
                'station' => $station,
                'progress' => $progress,
                'is_completed' => $progress && $progress->isStatusCompleted(),
                'completed_at' => $progress ? $progress->completed_at : null,
            ];
        }

        $nextStation = $this->getNextAvailableStation($participant, $stations);

        return [
            'total_stations' => $totalStations,
            'completed_stations' => $completedStations,
            'pending_stations' => $totalStations - $completedStations,
            'progress_percentage' => $progressPercentage,
            'is_completed' => $totalStations > 0 && $completedStations === $totalStations,
            'stations_progress' => $stationsProgress,
            'next_station' => $nextStation,
            'participant' => $participant,
            'quest' => $quest,
        ];
    }

    /**
     * Получить следующую доступную станцию для участника
     *
     * @param QuestParticipants $participant
     * @param array|null $stations Массив станций, если null - будет загружен из БД
     * @return QuestStations|null
     */
    public function getNextAvailableStation(QuestParticipants $participant, ?array $stations = null): ?QuestStations
    {
        if ($stations === null) {
            $quest = $participant->quest;
            $stations = QuestStations::find()
                ->where(['quest_id' => $quest->id])
                ->andWhere(['deleted_at' => null])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }

        foreach ($stations as $station) {
            $progress = StationProgress::findOne([
                'participant_id' => $participant->id,
                'station_id' => $station->id
            ]);

            if (!$progress || !$progress->isStatusCompleted()) {
                return $station;
            }
        }

        return null; // Все станции пройдены
    }

    /**
     * Проверить, завершен ли квест участником
     *
     * @param QuestParticipants $participant
     * @return bool
     */
    public function isQuestCompleted(QuestParticipants $participant): bool
    {
        $quest = $participant->quest;
        $totalStations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->count();

        if ($totalStations === 0) {
            return false;
        }

        $completedStations = StationProgress::find()
            ->where(['participant_id' => $participant->id])
            ->andWhere(['status' => StationProgress::STATUS_COMPLETED])
            ->count();

        return $completedStations === $totalStations;
    }

    /**
     * Получить прогресс команды по квесту
     *
     * @param QuestTeams $team
     * @return array
     */
    public function getTeamProgress(QuestTeams $team): array
    {
        $quest = $team->quest;
        $participants = QuestParticipants::find()
            ->where(['team_id' => $team->id])
            ->all();

        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $totalStations = count($stations);
        $teamCompletedStations = [];

        // Собираем информацию о прохождении станций командой
        foreach ($stations as $station) {
            $completedCount = StationProgress::find()
                ->innerJoin('quest_participants', 'station_progress.participant_id = quest_participants.id')
                ->where([
                    'quest_participants.team_id' => $team->id,
                    'station_progress.station_id' => $station->id,
                    'station_progress.status' => StationProgress::STATUS_COMPLETED
                ])
                ->count();

            $teamCompletedStations[] = [
                'station' => $station,
                'completed_by' => $completedCount,
                'total_members' => count($participants),
                'is_fully_completed' => $completedCount === count($participants) && count($participants) > 0,
            ];
        }

        $fullyCompleted = array_filter($teamCompletedStations, function($item) {
            return $item['is_fully_completed'];
        });

        $progressPercentage = $totalStations > 0 
            ? round((count($fullyCompleted) / $totalStations) * 100, 2) 
            : 0;

        // Подсчет общего количества очков команды
        $totalPoints = (int)(new Query())
            ->from('quest_participants')
            ->where(['team_id' => $team->id])
            ->sum('points');

        return [
            'team' => $team,
            'quest' => $quest,
            'total_stations' => $totalStations,
            'fully_completed_stations' => count($fullyCompleted),
            'progress_percentage' => $progressPercentage,
            'is_completed' => $totalStations > 0 && count($fullyCompleted) === $totalStations,
            'stations_progress' => $teamCompletedStations,
            'total_points' => $totalPoints ?? 0,
            'members' => $participants,
        ];
    }

    /**
     * Получить статистику квеста (для создателя/администратора)
     *
     * @param Quests $quest
     * @return array
     */
    public function getQuestStatistics(Quests $quest): array
    {
        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $participants = QuestParticipants::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['role' => QuestParticipants::ROLE_PLAYER])
            ->all();

        $totalParticipants = count($participants);
        $completedQuest = 0;
        $participantsProgress = [];

        foreach ($participants as $participant) {
            $progress = $this->getParticipantProgress($participant);
            $participantsProgress[] = $progress;

            if ($progress['is_completed']) {
                $completedQuest++;
            }
        }

        // Статистика по станциям
        $stationsStats = [];
        foreach ($stations as $station) {
            $completedCount = StationProgress::find()
                ->innerJoin('quest_participants', 'station_progress.participant_id = quest_participants.id')
                ->where([
                    'quest_participants.quest_id' => $quest->id,
                    'quest_participants.role' => QuestParticipants::ROLE_PLAYER,
                    'station_progress.station_id' => $station->id,
                    'station_progress.status' => StationProgress::STATUS_COMPLETED
                ])
                ->count();

            $stationsStats[] = [
                'station' => $station,
                'completed_by' => $completedCount,
                'completion_rate' => $totalParticipants > 0 
                    ? round(($completedCount / $totalParticipants) * 100, 2) 
                    : 0,
            ];
        }

        return [
            'quest' => $quest,
            'total_stations' => count($stations),
            'total_participants' => $totalParticipants,
            'completed_quest' => $completedQuest,
            'completion_rate' => $totalParticipants > 0 
                ? round(($completedQuest / $totalParticipants) * 100, 2) 
                : 0,
            'stations_statistics' => $stationsStats,
            'participants_progress' => $participantsProgress,
        ];
    }

    /**
     * Получить топ участников по очкам
     *
     * @param Quests $quest
     * @param int $limit
     * @return array
     */
    public function getTopParticipants(Quests $quest, int $limit = 10): array
    {
        $participants = QuestParticipants::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['role' => QuestParticipants::ROLE_PLAYER])
            ->orderBy(['points' => SORT_DESC])
            ->limit($limit)
            ->all();

        $result = [];
        foreach ($participants as $participant) {
            $progress = $this->getParticipantProgress($participant);
            $result[] = [
                'participant' => $participant,
                'progress' => $progress,
            ];
        }

        return $result;
    }

    /**
     * Получить топ команд по очкам
     *
     * @param Quests $quest
     * @param int $limit
     * @return array
     */
    public function getTopTeams(Quests $quest, int $limit = 10): array
    {
        $teams = QuestTeams::find()
            ->where(['quest_id' => $quest->id])
            ->all();

        $teamsProgress = [];
        foreach ($teams as $team) {
            $teamProgress = $this->getTeamProgress($team);
            $teamsProgress[] = $teamProgress;
        }

        // Сортировка по общему количеству очков
        usort($teamsProgress, function($a, $b) {
            return $b['total_points'] <=> $a['total_points'];
        });

        return array_slice($teamsProgress, 0, $limit);
    }

    /**
     * Инициализировать прогресс для нового участника (создать записи для всех станций)
     *
     * @param QuestParticipants $participant
     * @return int Количество созданных записей
     */
    public function initializeProgress(QuestParticipants $participant): int
    {
        $quest = $participant->quest;
        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->all();

        $created = 0;
        foreach ($stations as $station) {
            $existing = StationProgress::findOne([
                'participant_id' => $participant->id,
                'station_id' => $station->id
            ]);

            if (!$existing) {
                $progress = new StationProgress();
                $progress->participant_id = $participant->id;
                $progress->station_id = $station->id;
                $progress->status = StationProgress::STATUS_PENDING;
                if ($progress->save()) {
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Отметить станцию как пройденную
     *
     * @param QuestParticipants $participant
     * @param QuestStations $station
     * @param int $points Количество очков за прохождение
     * @return StationProgress|null
     */
    public function completeStation(QuestParticipants $participant, QuestStations $station, int $points = 0): ?StationProgress
    {
        // Проверяем, не пройдена ли уже станция
        $existingProgress = StationProgress::findOne([
            'participant_id' => $participant->id,
            'station_id' => $station->id,
            'status' => StationProgress::STATUS_COMPLETED
        ]);

        if ($existingProgress) {
            return $existingProgress;
        }

        $progress = StationProgress::findOne([
            'participant_id' => $participant->id,
            'station_id' => $station->id
        ]);

        if (!$progress) {
            $progress = new StationProgress();
            $progress->participant_id = $participant->id;
            $progress->station_id = $station->id;
        }

        $progress->status = StationProgress::STATUS_COMPLETED;
        $progress->completed_at = date('Y-m-d H:i:s');

        if ($progress->save()) {
            if ($points > 0) {
                $participant->updateCounters(['points' => $points]);
            }
            return $progress;
        }

        return null;
    }
}

