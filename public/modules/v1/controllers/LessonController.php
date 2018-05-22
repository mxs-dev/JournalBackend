<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{
    ActiveDataProvider, ArrayDataProvider
};
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\{ User };
use app\models\records\{ LessonRecord };

/**
 * Class LessonController
 * @package app\modules\v1\controllers
 */
class LessonController extends ActiveController
{
    /** @var LessonRecord */
    public $modelClass = LessonRecord::class;
    public $enableCsrfValidation = false;


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => CustomCors::class,
        ];

        $behaviors['verbs'] = [
            'class'   => VerbFilter::class,
            'actions' => [
                'index'  => ['GET'],
                'view'   => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE']
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => [ 'options', 'index']
        ];


        return $behaviors;
    }


    public function actions () {
        return [];
    }


    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => LessonRecord::find()
        ]);
    }


    /**
     * @param $id
     * @return LessonRecord
     * @throws HttpException
     */
    public function actionView ($id) {
        $model = LessonRecord::findOne($id);

        if (empty($model))
            throw new HttpException(404, "Not Found");

        return $model;
    }


    /**
     * @return LessonRecord
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = new LessonRecord();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $model->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

            return $model;
        }

        throw new HttpException(422, json_encode($model->getErrors()));
    }


    /**
     * @param $id integer
     * @return LessonRecord
     * @throws HttpException
     */
    public function actionUpdate ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = $this->actionView($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->save();
            $model->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

            return $model;
        }

        throw new HttpException(422, json_encode($model->getErrors()));
    }


    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id) {
        $model = $this->actionView($id);

        $model->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    public function actionOptions () {
        return 'ok';
    }
}