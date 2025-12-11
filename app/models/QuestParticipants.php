<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quest_participants".
 *
 * @property int $id
 * @property int $user_id
 * @property int $quest_id
 * @property int|null $team_id
 * @property string $role
 * @property int $points
 * @property string|null $banned
 * @property string $created_at
 *
 * @property Quests $quest
 * @property StationProgress[] $stationProgresses
 * @property QuestTeams $team
 * @property Users $user
 */
class QuestParticipants extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_OWNER = 'owner';
    const ROLE_VOLUNTEER = 'volunteer';
    const ROLE_PLAYER = 'player';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quest_participants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'banned'], 'default', 'value' => null],
            [['points'], 'default', 'value' => 0],
            [['user_id', 'quest_id', 'role'], 'required'],
            [['user_id', 'quest_id', 'team_id', 'points'], 'integer'],
            [['role'], 'string'],
            [['banned', 'created_at'], 'safe'],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['quest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quests::class, 'targetAttribute' => ['quest_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestTeams::class, 'targetAttribute' => ['team_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'quest_id' => 'Quest ID',
            'team_id' => 'Team ID',
            'role' => 'Role',
            'points' => 'Points',
            'banned' => 'Banned',
            'created_at' => 'Created At',
        ];
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
     * Gets query for [[StationProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStationProgresses()
    {
        return $this->hasMany(StationProgress::class, ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[Team]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(QuestTeams::class, ['id' => 'team_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_OWNER => 'owner',
            self::ROLE_VOLUNTEER => 'volunteer',
            self::ROLE_PLAYER => 'player',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function setRoleToOwner()
    {
        $this->role = self::ROLE_OWNER;
    }

    /**
     * @return bool
     */
    public function isRoleVolunteer()
    {
        return $this->role === self::ROLE_VOLUNTEER;
    }

    public function setRoleToVolunteer()
    {
        $this->role = self::ROLE_VOLUNTEER;
    }

    /**
     * @return bool
     */
    public function isRolePlayer()
    {
        return $this->role === self::ROLE_PLAYER;
    }

    public function setRoleToPlayer()
    {
        $this->role = self::ROLE_PLAYER;
    }

    /**
     * Получить прогресс участника по квесту
     * @return array
     */
    public function getProgress()
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getParticipantProgress($this);
    }

    /**
     * Получить количество пройденных станций
     * @return int
     */
    public function getCompletedStationsCount()
    {
        return StationProgress::find()
            ->where(['participant_id' => $this->id])
            ->andWhere(['status' => StationProgress::STATUS_COMPLETED])
            ->count();
    }

    /**
     * Получить общее количество станций в квесте
     * @return int
     */
    public function getTotalStationsCount()
    {
        return QuestStations::find()
            ->where(['quest_id' => $this->quest_id])
            ->andWhere(['deleted_at' => null])
            ->count();
    }

    /**
     * Получить процент выполнения квеста
     * @return float
     */
    public function getProgressPercentage()
    {
        $total = $this->getTotalStationsCount();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->getCompletedStationsCount();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Проверить, завершен ли квест
     * @return bool
     */
    public function isQuestCompleted()
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->isQuestCompleted($this);
    }

    /**
     * Получить следующую доступную станцию
     * @return QuestStations|null
     */
    public function getNextStation()
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getNextAvailableStation($this);
    }

    /**
     * Проверить, пройдена ли конкретная станция
     * @param int $stationId
     * @return bool
     */
    public function hasCompletedStation($stationId)
    {
        return StationProgress::find()
            ->where([
                'participant_id' => $this->id,
                'station_id' => $stationId,
                'status' => StationProgress::STATUS_COMPLETED
            ])
            ->exists();
    }

    /**
     * Получить прогресс по конкретной станции
     * @param int $stationId
     * @return StationProgress|null
     */
    public function getStationProgress($stationId)
    {
        return StationProgress::findOne([
            'participant_id' => $this->id,
            'station_id' => $stationId
        ]);
    }

    /**
     * Проверить, забанен ли участник
     * @return bool
     */
    public function isBanned()
    {
        return $this->banned !== null;
    }
}
