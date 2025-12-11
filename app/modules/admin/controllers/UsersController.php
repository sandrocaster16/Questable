<?php

namespace app\modules\admin\controllers;

use app\models\Users;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user && ($user->id === 1 || $user->isAdmin());
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'change-role' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException if trying to change role of user with id=1
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $oldRole = $model->role;
            if ($model->load($this->request->post())) {
                // Проверяем, не пытаемся ли изменить роль пользователя с id=1
                if ($id === 1 && $model->role !== Users::ROLE_ADMIN && $model->role !== Users::ROLE_ROOT) {
                    $model->role = Users::ROLE_ADMIN;
                    Yii::$app->session->setFlash('warning', 'Пользователь с ID=1 всегда должен быть администратором. Роль изменена на admin.');
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен.');
                    if ($oldRole !== $model->role) {
                        Yii::$app->session->setFlash('info', 'Роль пользователя изменена с "' . $oldRole . '" на "' . $model->role . '".');
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Быстрое изменение роли пользователя
     * @param int $id ID пользователя
     * @param string $role Новая роль
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionChangeRole($id, $role)
    {
        $model = $this->findModel($id);

        // Пользователь с id=1 всегда админ
        if ($id === 1 && $role !== Users::ROLE_ADMIN && $role !== Users::ROLE_ROOT) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить роль пользователя с ID=1.');
            return $this->redirect(['index']);
        }

        if (!in_array($role, array_keys(Users::optsRole()))) {
            Yii::$app->session->setFlash('error', 'Недопустимая роль.');
            return $this->redirect(['index']);
        }

        $oldRole = $model->role;
        $model->role = $role;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Роль пользователя успешно изменена с "' . $oldRole . '" на "' . $role . '".');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при изменении роли.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
