<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quest_stations".
 *
 * @property int $id
 * @property int $quest_id
 * @property string $name
 * @property string $type
 * @property string|null $content
 * @property string|null $options
 * @property string $qr_identifier
 * @property string $created_at
 * @property string|null $deleted_at
 *
 * @property Quests $quest
 * @property StationProgress[] $stationProgresses
 */
class QuestStations extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_INFO = 'info';
    const TYPE_QUIZ = 'quiz';
    const TYPE_CURATOR_CHECK = 'curator_check';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quest_stations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'options', 'deleted_at'], 'default', 'value' => null],
            [['quest_id', 'name', 'type', 'qr_identifier'], 'required'],
            [['quest_id'], 'integer'],
            [['type', 'content'], 'string'],
            [['options', 'created_at', 'deleted_at'], 'safe'],
            [['name', 'qr_identifier'], 'string', 'max' => 255],
            ['type', 'in', 'range' => array_keys(self::optsType())],
            [['qr_identifier'], 'unique'],
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
            'type' => 'Type',
            'content' => 'Content',
            'options' => 'Options',
            'qr_identifier' => 'Qr Identifier',
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
     * Gets query for [[StationProgresses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStationProgresses()
    {
        return $this->hasMany(StationProgress::class, ['station_id' => 'id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_INFO => 'info',
            self::TYPE_QUIZ => 'quiz',
            self::TYPE_CURATOR_CHECK => 'curator_check',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeInfo()
    {
        return $this->type === self::TYPE_INFO;
    }

    public function setTypeToInfo()
    {
        $this->type = self::TYPE_INFO;
    }

    /**
     * @return bool
     */
    public function isTypeQuiz()
    {
        return $this->type === self::TYPE_QUIZ;
    }

    public function setTypeToQuiz()
    {
        $this->type = self::TYPE_QUIZ;
    }

    /**
     * @return bool
     */
    public function isTypeCuratorcheck()
    {
        return $this->type === self::TYPE_CURATOR_CHECK;
    }

    public function setTypeToCuratorcheck()
    {
        $this->type = self::TYPE_CURATOR_CHECK;
    }
}
