<?php
namespace app\modules\v1\controllers;

use \Yii;
use app\models\User;
use app\models\forms\{
    EmailConfirmForm, SignUpForm, LoginForm
};

use yii\web\HttpException;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};


    class UserController extends ActiveController
{
    public $modelClass = User::class;
    public $enableCsrfValidation = false;


    public function behaviors () {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['get', 'post', 'update']
            ]
        ];

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className()
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => [ 'options', 'index', 'login', 'confirm-registration']
        ];

        return $behaviors;
    }


    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => User::find()->where(['!=', 'status', -1]),
        ]);
    }


    public function actionLogin () {
        $model = new LoginForm();

        if ($model -> load(Yii::$app->request->post()) && $model->login()){
            $user = $model->getUser();
            $user -> generateAccessTokenAfterLogin();

            $response = Yii::$app->getResponse();
            $response -> setStatusCode(200);

            $responseData = [
                'id' => $user->getId(),
                'accessToken' => $user->accessToken
            ];

            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }


    public function actionRegister () {
        $model = new SignUpForm();

        if ($model -> load(Yii::$app->request->post()) && $model->save()){
            $user = $model->getUser();

            $response = Yii::$app->getResponse();
            $response -> setStatusCode(200);

            $responseData = $user->toArray();

            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
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


    public function search () {
        //TODO разобраться как сделать поиск сразу по 3 параметрам так, чтобы пользовтель искал сразу по ФИО
    }


    public function actionOptions () {
        return 'OK';
    }
}