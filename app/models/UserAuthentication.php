<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_authentication".
 *
 * @property int $id
 * @property int $user_id
 * @property string $source
 * @property string $identifier
 * @property string $created_at
 *
 * @property Users $user
 */
class UserAuthentication extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const SOURCE_TELEGRAM = 'telegram';
    const SOURCE_EMAIL = 'email';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_authentication';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'identifier'], 'required'],
            [['user_id'], 'integer'],
            [['source'], 'string'],
            [['created_at'], 'safe'],
            [['identifier'], 'string', 'max' => 255],
            ['source', 'in', 'range' => array_keys(self::optsSource())],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'source' => 'Source',
            'identifier' => 'Identifier',
            'created_at' => 'Created At',
        ];
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
     * column source ENUM value labels
     * @return string[]
     */
    public static function optsSource()
    {
        return [
            self::SOURCE_TELEGRAM => 'telegram',
            self::SOURCE_EMAIL => 'email',
        ];
    }

    /**
     * @return string
     */
    public function displaySource()
    {
        return self::optsSource()[$this->source];
    }

    /**
     * @return bool
     */
    public function isSourceTelegram()
    {
        return $this->source === self::SOURCE_TELEGRAM;
    }

    public function setSourceToTelegram()
    {
        $this->source = self::SOURCE_TELEGRAM;
    }

    /**
     * @return bool
     */
    public function isSourceEmail()
    {
        return $this->source === self::SOURCE_EMAIL;
    }

    public function setSourceToEmail()
    {
        $this->source = self::SOURCE_EMAIL;
    }
}
