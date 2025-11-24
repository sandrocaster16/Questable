<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\Quests; // Имя модели по таблице
use app\models\QuestStations;
use app\models\forms\QuestForm;
use app\models\forms\StationForm;

class QuestController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    // Список квестов создателя (исключая удаленные)
    public function actionIndex()
    {
        $quests = Quests::find()
            ->where(['creator_id' => Yii::$app->user->id])
            ->andWhere(['delete_at' => null]) // Проверка Soft Delete для квестов
            ->all();

        return $this->render('index', ['quests' => $quests]);
    }

    public function actionCreate()
    {
        $form = new QuestForm();

        if ($form->load(Yii::$app->request->post())) {
            $form->coverFile = UploadedFile::getInstance($form, 'coverFile');
            $quest = new Quests();

            if ($form->save($quest)) {
                return $this->redirect(['update', 'id' => $quest->id]);
            }
        }
        return $this->render('create', ['model' => $form]);
    }

    public function actionUpdate($id)
    {
        $quest = $this->findModel($id);

        // Проверка прав
        if ($quest->creator_id !== Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('Доступ запрещен.');
        }

        $questForm = new QuestForm();
        $questForm->name = $quest->name;
        $questForm->description = $quest->description;

        if ($questForm->load(Yii::$app->request->post())) {
            $questForm->coverFile = UploadedFile::getInstance($questForm, 'coverFile');
            if ($questForm->save($quest)) {
                Yii::$app->session->setFlash('success', 'Квест сохранен');
                return $this->refresh();
            }
        }

        // Выбираем только активные станции (deleted_at IS NULL)
        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->all();

        return $this->render('update', [
            'quest' => $quest,
            'questForm' => $questForm,
            'stations' => $stations,
        ]);
    }

    // Удаление квеста (Soft Delete -> delete_at)
    public function actionDelete($id)
    {
        $quest = $this->findModel($id);
        if ($quest->creator_id == Yii::$app->user->id) {
            $quest->delete_at = date('Y-m-d H:i:s'); // Внимание: поле delete_at
            $quest->save(false);
        }
        return $this->redirect(['index']);
    }

    // Сохранение станции (AJAX)

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws \JsonException
     */
    public function actionSaveStation($quest_id, $id = null)
    {
        $form = new StationForm();
        $form->quest_id = $quest_id;

        if ($id) {
            $station = QuestStations::findOne(['id' => $id, 'deleted_at' => null]);
            if ($station) $form->loadFromModel($station);
        }

        if (Yii::$app->request->isPost && $form->load(Yii::$app->request->post())) {
            if ($form->save()) {
                if (Yii::$app->request->isAjax) return $this->asJson(['success' => true]);
                return $this->redirect(['update', 'id' => $quest_id]);
            }
        }

        return $this->renderAjax('_station_form', ['model' => $form]);
    }

    // Удаление станции (Soft Delete -> deleted_at)
    public function actionDeleteStation($id)
    {
        $station = QuestStations::findOne($id);
        if ($station) {
            $quest = Quests::findOne($station->quest_id);
            if ($quest && $quest->creator_id == Yii::$app->user->id) {
                $station->deleted_at = date('Y-m-d H:i:s'); // Внимание: поле deleted_at
                $station->save(false);
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Quests::findOne($id)) !== null && $model->delete_at === null) {
            return $model;
        }
        throw new NotFoundHttpException('Квест не найден.');
    }
}