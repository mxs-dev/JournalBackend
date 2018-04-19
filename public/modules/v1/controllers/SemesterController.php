<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\records\{ AcademicYearRecord, SemesterRecord };
use app\models\{ User };


/**
 * Class SemesterController
 * @package app\modules\v1\controllers
 */
class SemesterController extends ActiveController
{
    public $modelClass = SemesterRecord::class;
    public $enableCsrfValidation = false;


    public function behaviors () {
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
                'update' => ['PUT'],
                'delete' => ['DELETE'],
            ]
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


    /**
     * @return ActiveDataProvider
     */
    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => SemesterRecord::find()
        ]);
    }


    /**
     * @param $id
     * @return SemesterRecord|null
     * @throws HttpException
     */
    public function actionView ($id) {
        $model = SemesterRecord::findOne($id);

        if (empty($model))
            throw new HttpException(404, "Not Found");

        return $model;
    }


    /**
     * @return SemesterRecord
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = new SemesterRecord();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $model->refresh();

            Yii::$app->getResponse()->setStatusCode(201, "Created");
            return $model;
        } else {
            throw new HttpException(422, json_encode($model->getErrors()));
        }
    }


    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = $this->actionView($id);
        $model->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return 'ok';
    }


    /**
     * @param $id
     * @return SemesterRecord|null
     * @throws HttpException
     */
    public function actionUpdate ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = $this->actionView($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $model->refresh();

            return $model;
        }

        throw new HttpException(422, json_encode($model->getErrors()));
    }


    public function actionOptions () {
        return 'ok';
    }
}
