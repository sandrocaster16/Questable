<?php

namespace app\core\services;

use app\models\QuestParticipants;
use app\models\Quests;
use app\models\QuestsUsers;

class DataProvider
{
    public function getLastQuestsByUserId($userId) {
        $quest_ids = QuestParticipants::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->column();

        $quests = [];

        foreach ($quest_ids as $quest_id) {
            $quests[] = Quests::find()
                ->select(['name', 'cover_image_url', 'description'])
                ->where(['id' => $quest_id])
                ->all();
        }

        return $quests;
    }

    public function getPopularQuests() {
        $quests_id = QuestParticipants::find()
            ->select(['id'])
            ->groupBy('quest_id')
            ->orderBy(['COUNT(*)' => SORT_DESC])
            ->column();

        return $quests_id;
    }
}