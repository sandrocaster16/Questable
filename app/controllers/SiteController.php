<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\Quests;
use app\models\QuestParticipants;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        // 1. Если гость - отправляем на вход (или можно рендерить лендинг)
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['auth/login']);
        }

        $user = Yii::$app->user->identity;
        $userId = $user->id;

        // --- ЛОГИКА ПУТЕЙ К ФАЙЛАМ ---
        // Папка на диске (для проверки существования)
        $uploadPath = Yii::getAlias('@webroot/uploads/users_avatars/');
        // Ссылка для браузера
        $uploadUrl = Yii::getAlias('@web/uploads/users_avatars/');
        $uploadUrlLogo = Yii::getAlias('@web/uploads/');

        // 2. Логика Аватарки
        // Если у юзера есть ссылка в БД (например, от Telegram или загруженная)
        if (!empty($user->avatar_url)) {
            if (strpos($user->avatar_url, 'http') === 0) {
                $avatar_path = $user->avatar_url;
            } else {
                $avatar_path = $user->avatar_url;
            }
        } else {
            if (file_exists($uploadPath . 'avatar.jpeg')) {
                $avatar_path = $uploadUrl . 'avatar.jpeg';
            } else {
                $avatar_path = $uploadUrl . 'default.png';
            }
        }

        // 3. Логика Логотипа (как было в твоем коде: ID.jpeg)
        $logoFilename = $userId . '.jpeg';
        if (file_exists($uploadPath . $logoFilename)) {
            $logoUrl = $uploadUrlLogo . $logoFilename;
        } else {
            // Заглушка, если персонального лого нет
            $logoUrl = $uploadUrlLogo . 'default_logo.jpeg';
        }

        // --- ЗАГРУЗКА ДАННЫХ ИЗ БД ---

        // 4. История квестов пользователя
        // Ищем записи в quest_participants для этого юзера и подтягиваем данные квеста
        $historyQuery = QuestParticipants::find()
            ->where(['user_id' => $userId])
            ->with('quest') // Жадная загрузка связи с квестом
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        $user_history = [];
        foreach ($historyQuery as $participant) {
            if ($participant->quest) {
                // Преобразуем в массив, чтобы твой view ($quest['name']) работал
                $questArr = $participant->quest->toArray();
                // Добавим дату прохождения, если нужно
                $questArr['participation_date'] = $participant->created_at;
                $user_history[] = $questArr;
            }
        }

        // 5. Популярные квесты (просто последние 5 для примера)
        $popular_quests = Quests::find()
            ->where(['delete_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->asArray() // Сразу возвращаем массив
            ->all();

        return $this->render('index', [
            'user_history' => $user_history,
            'popular_quests' => $popular_quests,
            'logo_path' => $logoUrl,
            'avatar_path' => $avatar_path,
            'id' => $userId,
            'username' => $user->username,
        ]);
    }

    public function actionInfo() {
        return $this->render('info');
    }
}