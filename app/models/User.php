<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\base\BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $avatar_url;
    public $role;
    public $created_at;
    public $deleted_at;
    public $banned;

    public $password;
    public $authKey;
    public $accessToken;

    private static $admin = [
        'id'       => 0,
        'username' => null,
        'password' => null,
        'role'     => Users::ROLE_ROOT,
    ];

    public function init()
    {
        parent::init();
        if ($this->authKey === null) {
            $this->authKey = hash('sha256', (string)$this->id . ':' . (string)$this->username);
        }
    }

    private static function ensureAdminInit(): void
    {
        if (self::$admin['username'] === null) {
            self::$admin['username'] = getenv('ADMIN_USERNAME') ?: 'admin';

            $plain = getenv('ADMIN_PASSWORD') ?: null;
            self::$admin['password'] = $plain !== null
                ? Yii::$app->security->generatePasswordHash($plain)
                : null;
        }
    }

    public static function findIdentity($id)
    {
        self::ensureAdminInit();

        if ((string)self::$admin['id'] === (string)$id) {
            return new static([
                'id'       => self::$admin['id'],
                'username' => self::$admin['username'],
                'role'     => self::$admin['role'],
            ]);
        }

        $userAR = Users::findIdentity($id);

        if ($userAR) {
            return new static($userAR->getAttributes());
        }

        return null;
    }

    public static function findByUsername($username)
    {
        self::ensureAdminInit();

        if (strcasecmp((string)(self::$admin['username'] ?? ''), (string)$username) === 0) {
            return new static([
                'id'       => self::$admin['id'],
                'username' => self::$admin['username'],
                'role'     => self::$admin['role'],
            ]);
        }

        $userAR = Users::find()->where(['username' => $username])->one();

        if ($userAR) {
            return new static($userAR->getAttributes());
        }

        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->username;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        if ($this->id === self::$admin['id']) {
            return Yii::$app->security->validatePassword($password, self::$admin['password']);
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->id === self::$admin['id']
            || $this->role === Users::ROLE_ADMIN;
    }
}