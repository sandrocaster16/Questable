<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\QuestStations;
use yii\db\Exception;

class StationForm extends Model
{
    public $id;
    public $quest_id;
    public $name;
    public $type; // 'info', 'quiz', 'curator_check'
    public $content;

    // Поля для генерации JSON в options
    public $answers = [];
    public $correct_answer;

    public function rules()
    {
        return [
            [['quest_id', 'name', 'type'], 'required'],
            [['content'], 'string'],
            [['type'], 'in', 'range' => ['info', 'quiz', 'curator_check']],
            [['answers'], 'safe'], // массив
            [['correct_answer'], 'string'],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function loadFromModel($station)
    {
        $this->id = $station->id;
        $this->quest_id = $station->quest_id;
        $this->name = $station->name;
        $this->type = $station->type;
        $this->content = $station->content;

        // Распаковка JSON options
        $options = json_decode($station->options, true, 512, JSON_THROW_ON_ERROR);
        if ($this->type === 'quiz' && $options) {
            $this->answers = $options['options'] ?? [];
            $this->correct_answer = $options['correct_answer'] ?? null;
        }
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws \JsonException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        // Используем полное имя класса модели (предполагаем QuestStations по имени таблицы)
        $station = $this->id ? QuestStations::findOne($this->id) : new QuestStations();

        $station->quest_id = $this->quest_id;
        $station->name = $this->name;
        $station->type = $this->type;
        $station->content = $this->content;

        if ($station->isNewRecord) {
            $station->created_at = date('Y-m-d H:i:s');
            // Генерация уникального QR идентификатора
            $station->qr_identifier = Yii::$app->security->generateRandomString(16);
        }

        // Упаковка JSON
        $optionsData = null;
        if ($this->type === 'quiz') {
            $optionsData = [
                'options' => $this->answers, // массив строк
                'correct_answer' => $this->correct_answer
            ];
            // Важно: переводим массив в JSON строку для сохранения в БД
            $station->options = json_encode($optionsData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } else {
            $station->options = null;
        }

        return $station->save();
    }
}