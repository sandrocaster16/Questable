<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quest_teams".
 *
 * @property int $id
 * @property int $quest_id
 * @property string $name
 * @property int $leader_id
 * @property string $created_at
 *
 * @property Users $leader
 * @property Quests $quest
 * @property QuestParticipants[] $questParticipants
 */
class QuestTeams extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quest_teams';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quest_id', 'name', 'leader_id'], 'required'],
            [['quest_id', 'leader_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['leader_id' => 'id']],
            [['quest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quests::class, 'targetAttribute' => ['quest_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quest_id' => 'Quest ID',
            'name' => 'Name',
            'leader_id' => 'Leader ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Leader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(Users::class, ['id' => 'leader_id']);
    }

    /**
     * Gets query for [[Quest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuest()
    {
        return $this->hasOne(Quests::class, ['id' => 'quest_id']);
    }

    /**
     * Gets query for [[QuestParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestParticipants()
    {
        return $this->hasMany(QuestParticipants::class, ['team_id' => 'id']);
    }

    /**
     * Получить прогресс команды
     * @return array
     */
    public function getProgress()
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getTeamProgress($this);
    }

    /**
     * Получить общее количество очков команды
     * @return int
     */
    public function getTotalPoints()
    {
        $participants = $this->questParticipants;
        $totalPoints = 0;
        foreach ($participants as $participant) {
            $totalPoints += $participant->points;
        }
        return $totalPoints;
    }

    /**
     * Получить количество участников команды
     * @return int
     */
    public function getMembersCount()
    {
        return count($this->questParticipants);
    }
}
