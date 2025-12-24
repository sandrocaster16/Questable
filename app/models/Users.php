<?php

namespace app\models;

use app\models\query\UsersQuery;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string|null $avatar_url
 * @property string $role
 * @property string $created_at
 * @property string|null $deleted_at
 * @property string|null $banned
 *
 * @property QuestParticipants[] $questParticipants
 * @property QuestTeams[] $questTeams
 * @property Quests[] $quests
 * @property UserAuthentication[] $userAuthentications
 */
class Users extends \yii\db\ActiveRecord  implements IdentityInterface
{

    /**
     * ENUM field values
     */
    const ROLE_ROOT = 'root';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_VOLUNTEER = 'volunteer';

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
            [['avatar_url', 'deleted_at', 'banned'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 'user'],
            [['username'], 'required'],
            [['role'], 'string'],
            [['created_at', 'deleted_at', 'banned'], 'safe'],
            [['username'], 'string', 'max' => 255],
            [['avatar_url'], 'string', 'max' => 2048],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            ['role', 'validateRoleChange'],
        ];
    }

    /**
     * Валидация изменения роли
     * Пользователь с id=1 всегда должен быть админом
     */
    public function validateRoleChange($attribute, $params)
    {
        if ($this->id === 1 && $this->role !== self::ROLE_ADMIN && $this->role !== self::ROLE_ROOT) {
            $this->addError($attribute, 'Пользователь с ID=1 всегда должен быть администратором.');
        }
    }

    /**
     * Перед сохранением гарантируем, что пользователь с id=1 - админ
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Пользователь с id=1 всегда админ
            if ($this->id === 1 && $this->role !== self::ROLE_ADMIN && $this->role !== self::ROLE_ROOT) {
                $this->role = self::ROLE_ADMIN;
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'avatar_url' => 'Avatar Url',
            'role' => 'Role',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
            'banned' => 'Banned',
        ];
    }

    /**
     * Gets query for [[QuestParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestParticipants()
    {
        return $this->hasMany(QuestParticipants::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[QuestTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestTeams()
    {
        return $this->hasMany(QuestTeams::class, ['leader_id' => 'id']);
    }

    /**
     * Gets query for [[Quests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuests()
    {
        return $this->hasMany(Quests::class, ['creator_id' => 'id']);
    }

    /**
     * Gets query for [[UserAuthentications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuthentications()
    {
        return $this->hasMany(UserAuthentication::class, ['user_id' => 'id']);
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
            self::ROLE_VOLUNTEER => 'volunteer',
            self::ROLE_USER => 'user',
        ];
    }

    /**
     * Получить человекочитаемые названия ролей
     * @return array
     */
    public static function getRoleLabels()
    {
        return [
            self::ROLE_ROOT => 'Root (Супер-администратор)',
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_VOLUNTEER => 'Волонтер',
            self::ROLE_USER => 'Обычный пользователь',
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
     * Проверка, является ли пользователь администратором (root или admin)
     * Пользователь с id=1 всегда считается админом
     * @return bool
     */
    public function isAdmin()
    {
        return $this->id === 1 || $this->isRoleAdmin() || $this->isRoleRoot();
    }

    /**
     * Проверка, является ли пользователь волонтером или выше
     * @return bool
     */
    public function isVolunteerOrAbove()
    {
        return $this->isAdmin() || $this->isRoleVolunteer();
    }

    /**
     * Находит пользователя по ID.
     * Добавили проверку deleted_at => null (Soft Delete)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'deleted_at' => null]);
    }

    /**
     * Находит пользователя по токену (нужно для API, пока возвращаем null)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Возвращает ID пользователя
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает ключ авторизации (для "Запомнить меня")
     * Так как в твоей схеме БД нет поля auth_key, пока возвращаем null.
     * Для безопасности в будущем лучше добавить колонку auth_key varchar(32).
     */
    public function getAuthKey()
    {
        // return $this->auth_key; // Если добавишь колонку в БД
        return null;
    }

    /**
     * Проверяет ключ авторизации
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function find()
    {
        return (new UsersQuery(get_called_class()))->notDeleted();
    }

    public static function findAllUsers(): UsersQuery
    {
        return (new UsersQuery(get_called_class()));
    }
}
