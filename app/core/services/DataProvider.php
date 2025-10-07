<?php

namespace app\core\services;

use app\models\Quests;
use app\models\QuestsUsers;

class DataProvider
{
    public function getLastQuestsByUserId($userId) {
        $quest_ids = QuestsUsers::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC]);

        $quests = [];
        foreach ($quest_ids as $quest_id) {
            $quests[] = Quests::find()
                ->select(['name'])
                ->where(['id' => $quest_id]);
        }

        return $quests;
    }

    public function getPopularQuests() {
        $quests_id = Quests::find()
            ->select(['id'])
            ->groupBy('quest_id')
            ->orderBy(['COUNT(*)' => SORT_DESC])
            ->limit(10);

        return $quests_id;
    }
}