<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users; // Используй имя модели, сгенерированное Gii (Users или User)
use app\models\UserAuthentication; // Твоя модель для таблицы user_authentification
use app\models\SystemLog;
use app\models\enum\SystemLogType;

class AuthController extends Controller
{
    // Страница входа
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->render('login');
    }

    // Logout
    public function actionLogout()
    {
        if (!Yii::$app->user->isGuest) {
            $log = new SystemLog();
            $log->type = SystemLogType::UserLogout->value;
            $log->message = json_encode([
                'action' => 'user_logout',
                'user_id' => Yii::$app->user->identity->id,
                'username' => Yii::$app->user->identity->username ?? null,
                'timestamp' => date('Y-m-d H:i:s'),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $log->save(false);
        }

        Yii::$app->user->logout();
        return $this->goHome();
    }

    // Callback от Telegram
    public function actionTgCallback()
    {
        $data = Yii::$app->request->get();

        if (!isset($data['hash'])) {
            throw new \yii\web\BadRequestHttpException('Hash not found');
        }

        // 1. Проверка валидности данных (Security check)
        if (!$this->checkTelegramAuthorization($data)) {
            throw new \yii\web\ForbiddenHttpException('Data is NOT from Telegram');
        }

        // 2. Данные валидны, достаем инфо
        $tgId = $data['id'];
        $firstName = $data['first_name'];
        $username = $data['username'] ?? $firstName; // Username может быть не указан
        $photoUrl = $data['photo_url'] ?? null;

        // 3. Ищем пользователя в таблице user_authentification
        $auth = UserAuthentication::find()
            ->where(['source' => 'telegram', 'identifier' => (string)$tgId])
            ->one();

        if ($auth) {
            // Пользователь уже есть -> Логиним
            $user = Users::findOne($auth->user_id); // Или User::findOne...

            // (Опционально) Обновим аватарку, если она изменилась
            if ($user && $photoUrl && $user->avatar_url !== $photoUrl) {
                $user->avatar_url = $photoUrl;
                $user->save(false);
            }
        } else {
            // Пользователя нет -> Регистрируем
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // А. Создаем запись в users
                $user = new Users();
                $user->username = $username;
                $user->avatar_url = $photoUrl;
                $user->role = 'user';
                $user->created_at = date('Y-m-d H:i:s');

                if (!$user->save()) {
                    throw new \Exception('Ошибка создания пользователя');
                }

                // Б. Создаем запись в user_authentification
                $newAuth = new UserAuthentication();
                $newAuth->user_id = $user->id;
                $newAuth->source = 'telegram';
                $newAuth->identifier = (string)$tgId;
                $newAuth->created_at = date('Y-m-d H:i:s');

                if (!$newAuth->save()) {
                    throw new \Exception('Ошибка привязки Telegram');
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Ошибка регистрации: ' . $e->getMessage());
                return $this->redirect(['login']);
            }
        }

        // 4. Логин в Yii
        Yii::$app->user->login($user, 3600 * 24 * 30); // Запомнить на 30 дней

        return $this->goHome();
    }

    /**
     * Функция проверки хэша от Telegram
     * Документация: https://core.telegram.org/widgets/login#checking-authorization
     */
    private function checkTelegramAuthorization($auth_data)
    {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);

        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }

        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);

        $bot_token = Yii::$app->params['telegramBotToken'];
        $secret_key = hash('sha256', $bot_token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            return false;
        }

        if ((time() - $auth_data['auth_date']) > 86400) {
            return false; // Данные устарели (старше 24 часов)
        }

        return true;
    }
}