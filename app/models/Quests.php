<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quests".
 *
 * @property int $id
 * @property int $creator_id
 * @property string $name
 * @property string|null $description
 * @property string|null $cover_image_url
 * @property string $created_at
 * @property string|null $delete_at
 *
 * @property Users $creator
 * @property QuestParticipants[] $questParticipants
 * @property QuestStations[] $questStations
 * @property QuestTeams[] $questTeams
 */
class Quests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'cover_image_url', 'delete_at'], 'default', 'value' => null],
            [['creator_id', 'name'], 'required'],
            [['creator_id'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'delete_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['cover_image_url'], 'string', 'max' => 2048],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['creator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'creator_id' => 'Creator ID',
            'name' => 'Name',
            'description' => 'Description',
            'cover_image_url' => 'Cover Image Url',
            'created_at' => 'Created At',
            'delete_at' => 'Delete At',
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Users::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[QuestParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestParticipants()
    {
        return $this->hasMany(QuestParticipants::class, ['quest_id' => 'id']);
    }

    /**
     * Gets query for [[QuestStations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestStations()
    {
        return $this->hasMany(QuestStations::class, ['quest_id' => 'id']);
    }

    /**
     * Gets query for [[QuestTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestTeams()
    {
        return $this->hasMany(QuestTeams::class, ['quest_id' => 'id']);
    }

    /**
     * Получить статистику квеста
     * @return array
     */
    public function getStatistics()
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getQuestStatistics($this);
    }

    /**
     * Получить топ участников
     * @param int $limit
     * @return array
     */
    public function getTopParticipants($limit = 10)
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getTopParticipants($this, $limit);
    }

    /**
     * Получить топ команд
     * @param int $limit
     * @return array
     */
    public function getTopTeams($limit = 10)
    {
        $service = \Yii::$app->get('questProgressService', false);
        if (!$service) {
            $service = new \app\core\services\QuestProgressService();
        }
        return $service->getTopTeams($this, $limit);
    }

    /**
     * Получить количество активных участников (игроков)
     * @return int
     */
    public function getParticipantsCount()
    {
        return QuestParticipants::find()
            ->where(['quest_id' => $this->id])
            ->andWhere(['role' => QuestParticipants::ROLE_PLAYER])
            ->count();
    }

    /**
     * Получить количество станций
     * @return int
     */
    public function getStationsCount()
    {
        return QuestStations::find()
            ->where(['quest_id' => $this->id])
            ->andWhere(['deleted_at' => null])
            ->count();
    }
}
