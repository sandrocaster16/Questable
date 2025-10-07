<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property int $tg_id
 * @property string $role
 * @property string $created_at
 * @property string|null $deleted_at
 * @property string|null $banned_at
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
            [['deleted_at', 'banned_at'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 'user'],
            [['username', 'tg_id'], 'required'],
            [['tg_id'], 'integer'],
            [['role'], 'string'],
            [['created_at', 'deleted_at', 'banned_at'], 'safe'],
            [['username'], 'string', 'max' => 255],
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
            'username' => 'Username',
            'tg_id' => 'Tg ID',
            'role' => 'Role',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
            'banned_at' => 'Banned At',
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
