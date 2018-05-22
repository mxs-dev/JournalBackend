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
use app\models\records\{ TeachesRecord };

/**
 * Class TeachesController
 * @package app\modules\v1\controllers
 */
class TeachesController extends ActiveController
{
    public $modelClass = TeachesRecord::class;
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
                'update' => ['PUT', 'PATCH'],
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


    /**
     * @return ActiveDataProvider
     */
    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => TeachesRecord::find()
        ]);
    }


    /**
     * @param $id
     * @return TeachesRecord
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView ($id) {
        $model = TeachesRecord::findOne($id);

        if (empty($model))
            throw new HttpException(404, "Not Found");

        return $model;
    }


    public function actionOptions () {
        return 'ok';
    }
}