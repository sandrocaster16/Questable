<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quest_users".
 *
 * @property int $id
 * @property int $user_id
 * @property int $quest_id
 * @property string $role
 * @property int|null $command_id
 * @property int|null $points
 * @property string|null $banned
 * @property string|null $created_at
 *
 * @property Quests $quest
 * @property Users $user
 */
class QuestsUsers extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_PLAYER = 'player';
    const ROLE_OWNER = 'owner';
    const ROLE_VOLUNTEER = 'volunteer';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quest_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['command_id', 'banned'], 'default', 'value' => null],
            [['points'], 'default', 'value' => 0],
            [['user_id', 'quest_id', 'role'], 'required'],
            [['user_id', 'quest_id', 'command_id', 'points'], 'integer'],
            [['role'], 'string'],
            [['banned', 'created_at'], 'safe'],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'quest_id' => 'Quest ID',
            'role' => 'Role',
            'command_id' => 'Command ID',
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
            self::ROLE_PLAYER => 'player',
            self::ROLE_OWNER => 'owner',
            self::ROLE_VOLUNTEER => 'volunteer',
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
    public function isRolePlayer()
    {
        return $this->role === self::ROLE_PLAYER;
    }

    public function setRoleToPlayer()
    {
        $this->role = self::ROLE_PLAYER;
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
}
