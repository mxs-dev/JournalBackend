<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\models\User;
use app\filters\CustomCors;
use app\models\forms\user\{
    EmailConfirmForm, SignUpForm, LoginForm, EditForm
};

/**
 * Class UserController
 * @package app\modules\v1\controllers
 */
class UserController extends ActiveController
{
    /** @var User */
    public $modelClass = User::class;

    public $enableCsrfValidation = false;


    public function actions () {
        $actions = parent::actions();

        return [];
    }


    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => CustomCors::class,
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => [ 'index', 'login', 'confirm-registration']
        ];

        return $behaviors;
    }


    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return class_exists($this->modelClass);
        }

        return false;
    }


    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => User::find()->where(['!=', 'status', -1]),
        ]);
    }


    public function actionView (int $id) {
        $user = $this->modelClass::findOne($id);

        if ($user){
            Yii::$app->getResponse()->setStatusCode(201);
            return $user;
        }

        throw new HttpException(404, 'Not Found');
    }


    public function actionLogin () {
        $model = new LoginForm();

        if ($model -> load(Yii::$app->request->post()) && $model->login()){
            $user = $model->getUser();
            $user -> generateAccessTokenAfterLogin();

            Yii::$app->getResponse()->setStatusCode(200);

            $responseData = [
                'id' => $user->getId(),
                'accessToken' => $user->accessToken
            ];

            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }


    public function actionCreate () {
        //TODO проверить наличие прав на создание пользователей.

        $model = new SignUpForm();

        if ($model -> load(Yii::$app->request->post()) && $model->save()){
            $user = $model->getUser();

            Yii::$app->getResponse()->setStatusCode(201);

            $responseData = $user->toArray();

            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }


    public function actionUpdate (int $id) {
        $model = new EditForm();

        $model->load(Yii::$app->request->post());

        if ($model->validate() && $model->save()){
            Yii::$app->getResponse()->setStatusCode(200);
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }

        $user = $model->getUser();
        return $user;
    }


    public function actionConfirmRegistration () {

        $model = new EmailConfirmForm();

        if ($model->load(Yii::$app->request->post()) && $model->confirm()){
            $user = $model->getUser();
            $user -> generateAccessTokenAfterLogin();

            Yii::$app -> response -> setStatusCode(200);

            return [
                'id'          => $user -> id,
                'accessToken' => $user -> accessToken
            ];
        }

        throw new HttpException(422, json_encode($model->errors));
    }


    public function actionDelete ($id) {
        //TODO проверить права!

        $model = $this->actionView($id);

        if ($model) {
            $model->status = User::STATUS_DELETED;

            if(!$model->save(false)){
                throw new HttpException(500, "Failed to delete the object for unknown reason.");
            }

            Yii::$app->getResponse()->setStatusCode(204);

            return 'ok';
        }

        throw new HttpException(404, 'Not Found.');
    }


    public function actionSearch () {
        //TODO разобраться как сделать поиск сразу по 3 параметрам так, чтобы пользовтель искал сразу по ФИО
    }


    public function checkAccess($action, $model = null, $params = [])
    {
        //TODO добавить код проверки доступа к методам API на основе RBAC.
        return true;
    }
}