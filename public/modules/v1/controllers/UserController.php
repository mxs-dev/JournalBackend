<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 10.12.2017
 * Time: 21:30
 */

namespace app\modules\v1\controllers;

use app\models\User;
use \Yii;
use app\models\LoginForm;
use yii\filters\auth\CompositeAuth;
use app\filters\auth\HttpBearerAuth;
use yii\web\HttpException;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = User::class;

    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => ['index', 'login']
        ];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['get', 'post', 'update']
            ]
        ];

        return $behaviors;
    }

    public function actionIndex () {
        return debug(\Yii::$app->user);
    }

    public function actionLogin () {
        $model = new LoginForm();

        if ($model -> load(Yii::$app->request->post()) && $model->login()){
            $user = $model->getUser();
            $user -> generateAccessTokenAfterUpdatingUserInfo();

            $response = Yii::$app->getResponse();
            $response -> setStatusCode(200);

            $id = implode(',', array_values($user->getPrimaryKey(true)));

            $responseData = [
                'id' => (int)$id,
                'access_token' => $user->accessToken
            ];

            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }
}