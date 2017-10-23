<?php

namespace app\controllers;

use app\models\forms\NewPasswordForm;
use app\models\forms\ResetPasswordForm;
use app\models\User;
use app\models\UsersSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administration'],
                    ],
                    [
                        'actions' => ['profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['registration', 'reset-password', 'registration-validate'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        $model->setScenario(User::SCENARIO_CREATE);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model
                ]);
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario(User::SCENARIO_UPDATE);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model
                ]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionToggleActive($id)
    {
        $model = $this->findModel($id);
        if (!is_null($model)) {
            $model->is_active = !$model->is_active;
            $model->save();
        }
        return $model->is_active;
    }

    /**
     * Настройка пользователя
     */
    public function actionProfile()
    {
        $model = $this->findModel(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Настройки сохранены');
        }
        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($id = null, $code = null)
    {
        $model = new ResetPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->user->sendResetPassword();
            Yii::$app->session->addFlash('success', 'Мы выслали вам ссылку для сбора пароля, на почту указанную при регистрации');
            return $this->redirect(['/']);
        }

        if (!empty($id) && !empty($code)) {
            $user = User::findOne($id);
            if ($user->password_reset_code == $code) {
                $model = new NewPasswordForm();

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $user->updatePassword($model->password);
                    Yii::$app->session->addFlash('success', 'Пароль изменен');
                    return $this->redirect(['/']);
                }

                return $this->render('new-password', ['model' => $model]);
            } else {
                Yii::$app->session->addFlash('danger', 'Ссылка для восстановления пароля - не корректна');
            }
        }

        return $this->render('reset-password', ['model' => $model]);
    }

    public function actionRegistrationValidate($id, $code)
    {
        $user = User::findOne(['id' => $id]);

        if (is_null($user)) {
            throw new NotFoundHttpException('Такой пользователь не найден');
        }

        $user->setScenario(User::SCENARIO_VALIDATION);

        if ($user->getRegistrationCode() !== $code) {
            Yii::$app->session->addFlash('danger', 'не корректный код активации');
        } else {
            $user->is_active = 1;
            $user->is_validated = 1;
            if ($user->save()) {
                Yii::$app->session->addFlash('success', 'Спасибо за регистрацию, ваш E-Mail подтвержден');
            } else {
                Yii::$app->session->addFlash('danger', 'Не удалось активировать УЗ');
            }
        }

        return $this->redirect(['/site/login']);
    }

    public function actionRegistration()
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTRATION);

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->addFlash('success', 'Проверьте почту, мы выслали вам ссылку для подтверждения регистрации, перейдите по ней.');
        }

        return $this->render('registration', ['user' => $user]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
