<?php

namespace app\modules\v1\controllers;

use \Yii;

use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\records\{ GroupRecord };

class GroupController extends ActiveController
{
    /** @var string GroupRecord */
    public $modelClass = GroupRecord::class;
    public $enableCsrfValidation = false;


    public function behaviors () {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => CustomCors::class,
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => [ 'options' ]
        ];


        return $behaviors;
    }


    public function actions () {
        $actions = parent::actions();

        unset($actions['delete']);
        unset($actions['create']);

        return $actions;
    }


    public function actionCreate () {
        //TODO не забыть узнать есть ли разрешение у пользователя создавать группы

        $group = new GroupRecord();

        if ($group->load(Yii::$app->request->post()) && $group->validate()){
            $group->save();

            return $group->toArray();
        } else {

            throw new HttpException(422, json_encode($group->errors));
        }
    }


    public function actionDelete () {
        //TODO не забыть узнать есть ли разрешение у пользователя удалять группы

        return "DELETE";
    }
}