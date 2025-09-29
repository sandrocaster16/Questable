<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quests".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $deleted_at
 *
 * @property QuestsStations[] $questsStations
 * @property QuestsUsers[] $questsUsers
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
            [['deleted_at'], 'default', 'value' => null],
            [['name'], 'required'],
            [['created_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[QuestsStations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestsStations()
    {
        return $this->hasMany(QuestsStations::class, ['quest_id' => 'id']);
    }

    /**
     * Gets query for [[QuestsUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestsUsers()
    {
        return $this->hasMany(QuestsUsers::class, ['quest_id' => 'id']);
    }

}
