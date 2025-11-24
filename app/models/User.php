<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\base\BaseObject implements IdentityInterface
{
    public $id;
    public $login;
    public $password;
    public $token = '';

    private $authKey;

    private static $admin = [
        'id'       => null,
        'login'    => null,
        'password' => null,
    ];

    public function __construct($config = [])
    {
        self::ensureAdminInit();
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->authKey = hash('sha256', (string)$this->id . ':' . (string)$this->login);
    }

    public static function findIdentity($id)
    {
        self::ensureAdminInit();

        if ((string)self::$admin['id'] === (string)$id) {
            return new static([
                'id'           => self::$admin['id'],
                'login'        => self::$admin['login']
            ]);
        }

        $userAR = Users::findOne($id);
        if ($userAR) {
            return new static([
                'id'           => $userAR->id,
                'login'        => $userAR->username
            ]);
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    private static function ensureAdminInit(): void
    {
        if (self::$admin['login'] === null) {
            self::$admin['id'] = 0;
            self::$admin['login'] = getenv('ADMIN_USERNAME') ?: 'admin';

            $plain = getenv('ADMIN_PASSWORD') ?: null;
            self::$admin['password'] = $plain !== null
                ? Yii::$app->security->generatePasswordHash($plain)
                : null;
        }
    }

    public static function findByUsername($login)
    {
        self::ensureAdminInit();

        if (strcasecmp((string)(self::$admin['login'] ?? ''), (string)$login) === 0) {
            return new static([
                'id'           => self::$admin['id'],
                'login'        => self::$admin['login']
            ]);
        }

        $userAR = Users::find()->where(['login' => $login])->one();
        if ($userAR) {
            return new static([
                'id'           => $userAR->id,
                'login'        => $userAR->login
            ]);
        }
        return null;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function setUsername($value)
    {
        $this->login = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function isAdmin(): bool
    {
        return $this->id === self::$admin['id'];
    }
}