<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quests_questions".
 *
 * @property int $id
 * @property int $station_id
 * @property string $question
 * @property string|null $answer
 * @property string|null $help
 * @property string|null $message
 * @property string|null $created_at
 * @property string|null $deleted_at
 *
 * @property QuestsStations $station
 */
class QuestsQuestions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quests_questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['answer', 'help', 'message', 'deleted_at'], 'default', 'value' => null],
            [['station_id', 'question'], 'required'],
            [['station_id'], 'integer'],
            [['answer'], 'string'],
            [['created_at', 'deleted_at'], 'safe'],
            [['question', 'help', 'message'], 'string', 'max' => 2048],
            [['station_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestsStations::class, 'targetAttribute' => ['station_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'station_id' => 'Station ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'help' => 'Help',
            'message' => 'Message',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[Station]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStation()
    {
        return $this->hasOne(QuestsStations::class, ['id' => 'station_id']);
    }

}
