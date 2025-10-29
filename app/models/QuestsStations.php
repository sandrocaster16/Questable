<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stations".
 *
 * @property int $id
 * @property int $quest_id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $deleted_at
 *
 * @property Quests $quest
 * @property QuestsQuestions[] $questsQuestions
 */
class QuestsStations extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deleted_at'], 'default', 'value' => null],
            [['quest_id', 'name'], 'required'],
            [['quest_id'], 'integer'],
            [['created_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
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
     * Gets query for [[QuestsQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestsQuestions()
    {
        return $this->hasMany(QuestsQuestions::class, ['station_id' => 'id']);
    }

}
