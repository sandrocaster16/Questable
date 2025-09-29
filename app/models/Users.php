<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int $user_id
 * @property int $quest_id
 * @property string $role
 * @property int|null $command_id
 * @property int|null $points
 * @property string|null $banned_at
 * @property string $created_at
 *
 * @property QuestsUsers[] $questsUsers
 */
class Users extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_ROOT = 'root';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['command_id', 'points', 'banned_at'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 'user'],
            [['user_id', 'quest_id'], 'required'],
            [['user_id', 'quest_id', 'command_id', 'points'], 'integer'],
            [['role'], 'string'],
            [['banned_at', 'created_at'], 'safe'],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
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
            'banned_at' => 'Banned At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[QuestsUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestsUsers()
    {
        return $this->hasMany(QuestsUsers::class, ['user_id' => 'id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_ROOT => 'root',
            self::ROLE_ADMIN => 'admin',
            self::ROLE_USER => 'user',
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
    public function isRoleRoot()
    {
        return $this->role === self::ROLE_ROOT;
    }

    public function setRoleToRoot()
    {
        $this->role = self::ROLE_ROOT;
    }

    /**
     * @return bool
     */
    public function isRoleAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function setRoleToAdmin()
    {
        $this->role = self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isRoleUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function setRoleToUser()
    {
        $this->role = self::ROLE_USER;
    }
}
