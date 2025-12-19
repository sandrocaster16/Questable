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

    public array $answers = [];
    public array $correct_answers = [];

    public function rules()
    {
        return [
            [['quest_id', 'name', 'type'], 'required'],
            [['content'], 'string'],
            [['id'], 'integer'],

            [['answers', 'correct_answers'], 'required',
                'when' => fn($model) => $model->type === QuestStations::TYPE_QUIZ,
                'whenClient' => "function () {
                return $('#station-type-select').val() === 'quiz';
            }"
            ],

            ['answers', 'each', 'rule' => ['string']],
            ['correct_answers', 'each', 'rule' => ['integer']],

            ['correct_answers', 'validateCorrectAnswer'],
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

        if ($station->type === QuestStations::TYPE_QUIZ && $station->options) {
            $options = json_decode($station->options, true);
            $this->answers = $options['answers'] ?? [];
            $this->correct_answers = (array)($options['correct_answers'] ?? []);
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

        if ($station->isNewRecord) {
            $station->qr_identifier = Yii::$app->security->generateRandomString(32);
        }

        if (!$station->save()) {
            return false;
        }

        if ($station->type === QuestStations::TYPE_CURATOR_CHECK) {
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
            $optionsData = [
                'answers' => array_values(array_filter($this->answers, fn($a) => trim($a) !== '')),
                'correct_answers' => array_values($this->correct_answers),
            ];

            $station->options = json_encode($optionsData, JSON_UNESCAPED_UNICODE);
        } else {
            $station->options = null;
        }

        return $station->save();
    }

    public function validateCorrectAnswer()
    {
        if ($this->type !== QuestStations::TYPE_QUIZ) {
            return;
        }

        if (empty($this->correct_answers)) {
            $this->addError('correct_answers', 'Не выбран правильный ответ');
            return;
        }

        foreach ($this->correct_answers as $index) {
            if (!isset($this->answers[$index]) || trim($this->answers[$index]) === '') {
                $this->addError('correct_answers', 'Выбран некорректный вариант ответа');
                return;
            }
        }
    }

}