<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\Quests;
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

    public function actionIndex()
    {
        $quests = Quests::find()
            ->where(['creator_id' => Yii::$app->user->id])
            ->andWhere(['delete_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
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
                Yii::$app->session->setFlash('success', 'Квест создан! Теперь добавьте станции.');
                return $this->redirect(['update', 'id' => $quest->id]);
            }
        }

        return $this->render('create', ['model' => $form]);
    }

    public function actionUpdate($id)
    {
        $quest = $this->findModel($id);

        if ($quest->creator_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Доступ запрещен.');
        }

        $questForm = new QuestForm();
        $questForm->name = $quest->name;
        $questForm->description = $quest->description;

        if ($questForm->load(Yii::$app->request->post())) {
            $questForm->coverFile = UploadedFile::getInstance($questForm, 'coverFile');
            if ($questForm->save($quest)) {
                Yii::$app->session->setFlash('success', 'Основные настройки сохранены');
                return $this->refresh();
            }
        }

        $stations = QuestStations::find()
            ->where(['quest_id' => $quest->id])
            ->andWhere(['deleted_at' => null])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'quest' => $quest,
            'questForm' => $questForm,
            'stations' => $stations,
        ]);
    }

    public function actionDelete($id)
    {
        $quest = $this->findModel($id);
        if ($quest->creator_id == Yii::$app->user->id) {
            $quest->delete_at = date('Y-m-d H:i:s');
            $quest->save(false);
        }
        return $this->redirect(['index']);
    }

    public function actionSaveStation($quest_id, $id = null)
    {
        $quest = $this->findModel($quest_id);
        if ($quest->creator_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $form = new StationForm();
        $form->quest_id = $quest_id;

        if ($id) {
            $station = QuestStations::findOne(['id' => $id, 'deleted_at' => null]);
            if ($station) $form->loadFromModel($station);
        }

        if (Yii::$app->request->isPost && $form->load(Yii::$app->request->post())) {
            if ($form->save()) {
                if (Yii::$app->request->isAjax) {
                    return $this->asJson(['success' => true]);
                }
                return $this->redirect(['update', 'id' => $quest_id]);
            }
        }

        return $this->renderAjax('_station_form', ['model' => $form]);
    }

    public function actionDeleteStation($id)
    {
        $station = QuestStations::findOne($id);
        if ($station) {
            $quest = Quests::findOne($station->quest_id);
            if ($quest && $quest->creator_id == Yii::$app->user->id) {
                $station->deleted_at = date('Y-m-d H:i:s');
                $station->save(false);
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionStatistics($id)
    {
        $quest = $this->findModel($id);

        if ($quest->creator_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Доступ запрещен.');
        }

        $statistics = $quest->getStatistics();

        return $this->render('statistics', [
            'quest' => $quest,
            'statistics' => $statistics,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Quests::findOne($id)) !== null && $model->delete_at === null) {
            return $model;
        }
        throw new NotFoundHttpException('Квест не найден.');
    }
}