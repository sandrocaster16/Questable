<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "station_progress".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $station_id
 * @property string $status
 * @property string|null $completed_at
 *
 * @property QuestParticipants $participant
 * @property QuestStations $station
 */
class StationProgress extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'station_progress';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['completed_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['participant_id', 'station_id'], 'required'],
            [['participant_id', 'station_id'], 'integer'],
            [['status'], 'string'],
            [['completed_at'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestParticipants::class, 'targetAttribute' => ['participant_id' => 'id']],
            [['station_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestStations::class, 'targetAttribute' => ['station_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'station_id' => 'Station ID',
            'status' => 'Status',
            'completed_at' => 'Completed At',
        ];
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(QuestParticipants::class, ['id' => 'participant_id']);
    }

    /**
     * Gets query for [[Station]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStation()
    {
        return $this->hasOne(QuestStations::class, ['id' => 'station_id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_COMPLETED => 'completed',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function setStatusToCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
    }
}
