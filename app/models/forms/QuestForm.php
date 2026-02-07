<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Quests;
use yii\helpers\Html;
use yii\web\UploadedFile;

class QuestForm extends Model
{
    public $name;
    public $description;
    public $coverFile;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['coverFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    public function save(Quests $quest)
    {
        if (!$this->validate()) {
            return false;
        }

        $quest->name = Html::encode($this->name);
        $quest->description = Html::encode($this->description);

        if ($quest->isNewRecord) {
            $quest->creator_id = Yii::$app->user->id;
            $quest->created_at = date('Y-m-d H:i:s');
        }

        if ($this->coverFile) {
            $dir = Yii::getAlias('@webroot/uploads/');
            if (!is_dir($dir)) if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }

            $fileName = 'quest_' . uniqid('', true) . '.' . $this->coverFile->extension;
            if ($this->coverFile->saveAs($dir . $fileName)) {
                $quest->cover_image_url = '/uploads/' . $fileName;
            }
        }

        return $quest->save();
    }
}