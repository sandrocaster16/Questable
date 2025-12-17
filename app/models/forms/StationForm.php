<?php

namespace app\models\forms;

use app\models\enum\SystemLogType;
use app\models\SystemLog;
use Yii;
use yii\base\Model;
use app\models\QuestStations;

class StationForm extends Model
{
    public $id;
    public $quest_id;
    public $name;
    public $type;
    public $content;

    public $answers_raw;
    public $correct_answer;

    public function rules()
    {
        return [
            [['quest_id', 'name', 'type'], 'required'],
            [['content'], 'string'],
            [['id'], 'integer'],

            [['answers_raw', 'correct_answer'], 'required', 'when' => function($model) {
                return $model->type === QuestStations::TYPE_QUIZ;
            }, 'whenClient' => "function (attribute, value) {
                return $('#station-type-select').val() == 'quiz';
            }"],

            [['answers_raw', 'correct_answer'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название станции',
            'type' => 'Тип задания',
            'content' => 'Текст задания',
            'answers_raw' => 'Варианты ответов',
            'correct_answer' => 'Правильный ответ',
        ];
    }

    public function loadFromModel(QuestStations $station)
    {
        $this->id = $station->id;
        $this->quest_id = $station->quest_id;
        $this->name = $station->name;
        $this->type = $station->type;
        $this->content = $station->content;

        if ($station->type === QuestStations::TYPE_QUIZ && !empty($station->options)) {
            $options = json_decode($station->options, true);
            if (isset($options['answers']) && is_array($options['answers'])) {
                $this->answers_raw = implode("\n", $options['answers']);
            }
            $this->correct_answer = $options['correct_answer'] ?? '';
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $station = $this->id ? QuestStations::findOne($this->id) : new QuestStations();

        $station->quest_id = $this->quest_id;
        $station->name = $this->name;
        $station->type = $this->type;
        $station->content = $this->content;

        $isNew = $station->isNewRecord;

        if (!$station->save()) {
            return false;
        }

        if ($isNew && $station->type === QuestStations::TYPE_CURATOR_CHECK) {
            $token = Yii::$app->security->generateRandomString(32);

            $log = new SystemLog();
            $log->type = SystemLogType::StationAdminRegistration->value;
            $log->message = json_encode([
                'token' => $token,
                'station_id' => $station->id,
                'quest_id' => $station->quest_id,
                'is_used' => false,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

            $log->save(false);
        }


        if ($this->type === QuestStations::TYPE_QUIZ) {
            $answersArray = array_filter(array_map('trim', explode("\n", $this->answers_raw)));

            $optionsData = [
                'answers' => array_values($answersArray),
                'correct_answer' => trim($this->correct_answer)
            ];

            $station->options = json_encode($optionsData, JSON_UNESCAPED_UNICODE);
        } else {
            $station->options = null;
        }

        return $station->save();
    }
}