<?php
namespace app\modules\v1\controllers;


use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\User;
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
        return [];
    }


    public function actionIndex () {
        return new ActiveDataProvider([
           'query' => GroupRecord::find()
        ]);
    }


    public function actionView ($id) {
        $group = GroupRecord::findOne($id);

        if (empty($group))
            throw new HttpException(404, "Not Found");

        return $group;
    }


    public function actionDelete ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $group = $this->actionView($id);

        if (empty($group))
            throw new HttpException(404, "Not Found");

        $group->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return 'ok';
    }


    public function actionTest(){
        $group = GroupRecord::find(4)->innerJoin('studying', 'group.id=group.id')->innerJoin('user', 'user.id=studying.userId')->one();



        return $group->students;
    }


    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $group = new GroupRecord();

        if ($group->load(Yii::$app->request->post()) && $group->validate()){
            $group->save();

            Yii::$app->getResponse()->setStatusCode(201);

            return $group;
        } else {

            throw new HttpException(422, json_encode($group->errors));
        }
    }
}