<?php

namespace app\modules\tg_api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Users;
use app\models\QuestsUsers;

class TelegramController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $body = $request->getRawBody();
        $data = json_decode($body, true);

        $tgData = $data['tg_data'] ?? null;
        $questId = $data['quest_id'] ?? null;

        Yii::info(json_encode($data), 'telegram-requests');


        if (!$tgData || !$questId || !isset($tgData['id'])) {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'error', 'message' => 'Missing required parameters.', 'data' => $data];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user = Users::findOne(['tg_id' => $tgData['id']]);

            if (!$user) {
                $user = new Users();
                $user->tg_id = (int)$tgData['id'];
                $user->username = $tgData['username'] ?? ('user' . $tgData['id']);
                if (!$user->save()) {
                    throw new \Exception('Failed to save user: ' . json_encode($user->errors));
                }
            }

            $isAlreadyRegistered = QuestsUsers::find()
                ->where(['user_id' => $user->id, 'quest_id' => $questId])
                ->exists();

            if ($isAlreadyRegistered) {
                return ['status' => 'ok', 'message' => 'Вы уже записаны на этот квест.'];
            }

            $questUser = new QuestsUsers();
            $questUser->user_id = $user->id;
            $questUser->quest_id = (int)$questId;
            $questUser->role = QuestsUsers::ROLE_PLAYER;

            if (!$questUser->save()) {
                throw new \Exception('Failed to save quest user: ' . json_encode($questUser->errors));
            }

            $transaction->commit();
            return ['status' => 'success', 'message' => 'Вы успешно зарегистрированы на квест!'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage(), 'telegram-errors');
            return ['status' => 'error', 'message' => 'Internal server error. Please contact support.'];
        }
    }
}