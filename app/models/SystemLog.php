<?php

namespace app\models;

use app\models\enum\SystemLogType;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $type
 * @property string|null $message
 * @property string $created_at
 */
class SystemLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%system_logs}}';
    }

    public function rules(): array
    {
        return [
            [['type'], 'required'],
            [['message'], 'string'],
            [['type'], 'in', 'range' => array_column(SystemLogType::cases(), 'value')],
            [['type'], 'string', 'max' => 32],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'message' => 'Сообщение',
            'created_at' => 'Дата создания',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!$insert) {
            return false;
        }

        return parent::beforeSave($insert);
    }
}
