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
use app\models\{ User, Teacher, Student };
use app\models\records\{ SubjectRecord };


/**
 * Class SubjectController
 * @package app\modules\v1\controllers
 */
class SubjectController extends ActiveController
{
    /** @var string */
    public $modelClass = SubjectRecord::class;
    public $enableCsrfValidation = false;

    public function behaviors()
    {
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
            'query' => SubjectRecord::find()
        ]);
    }


    /**
     * @param $id
     * @return null|SubjectRecord
     * @throws HttpException
     */
    public function actionView ($id) {
        $model = SubjectRecord::findOne($id);

        if (empty($model))
            throw new HttpException(404, "Not found");

        return $model;
    }


    /**
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = new SubjectRecord();

        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->save();

            return SubjectRecord::findOne($model->id);
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }


    /**
     * @param $id
     * @return mixed
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = $this->actionView($id);

        if (empty($model))
            throw new HttpException(404, "Not Found");

        $model->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    public function actionOptions () {
        return "ok";
    }
}