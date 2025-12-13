<?php

namespace app\models\forms;

use app\models\Users;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ProfileForm extends Model
{
    public $nickname;

    /**
     * @var UploadedFile
     */
    public $avatar;

    private $_user;

    public function __construct($user, $config = [])
    {
        $this->_user = $user;
        $this->nickname = $user->username; // Заполняем текущим ником
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['nickname', 'required', 'message' => 'Никнейм не может быть пустым.'],
            ['nickname', 'string', 'min' => 2, 'max' => 255],
            // Проверка на уникальность, исключая текущего пользователя
            ['nickname', 'unique', 'targetClass' => Users::class, 'targetAttribute' => 'username',
                'filter' => ['!=', 'id', $this->_user->id], 'message' => 'Этот никнейм уже занят.'
            ],

            ['avatar', 'image', 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 5, 'skipOnEmpty' => true],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->username = $this->nickname;

        // Если файл был загружен
        if ($this->avatar) {
            $dir = Yii::getAlias('@webroot/uploads/avatars/');

            // Создаем папку, если нет
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }
            }

            // Генерируем имя файла
            $fileName = 'user_' . $user->id . '_' . time() . '.' . $this->avatar->extension;

            // Сохраняем файл
            if ($this->avatar->saveAs($dir . $fileName)) {
                // Удаляем старый аватар, если он есть и это не дефолтная картинка
                // if ($user->avatar_url && file_exists(Yii::getAlias('@webroot') . $user->avatar_url)) {
                //    unlink(Yii::getAlias('@webroot') . $user->avatar_url);
                // }

                // Обновляем путь в БД (предполагается, что поле называется avatar_url)
                $user->avatar_url = '/uploads/avatars/' . $fileName;
            }
        }

        return $user->save();
    }
}