<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\models\User;
use app\models\records\{ GroupRecord, GradeRecord};
use app\filters\CustomCors;


/**
 * Class GradeController
 * @package app\modules\v1\controllers
 */
class GradeController extends ActiveController
{
    /** @var string GradeRecord */
    public $modelClass = GradeRecord::class;
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

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => GradeRecord::find()
        ]);
    }

    /**
     * @param $id
     * @return null|GradeRecord
     * @throws HttpException
     */
    public function actionView ($id) {
        //TODO проверить имеет ли пользователь доступ к данной оценке
        $grade = GradeRecord::findOne($id);

        if (empty($grade))
            throw new HttpException(404, "Not found");

        return $grade;
    }

    /**
     * @return GradeRecord
     * @throws HttpException
     */
    public function actionCreate () {
        //TODO проверить может ли пользователь выставлять оценки по данному предмету

        $grade = new GradeRecord();

        if ($grade->load(Yii::$app->request->post()) && $grade->validate()){
            $grade->save();

            Yii::$app->getResponse()->setStatusCode(200);

            return $grade;
        } else {
            throw new HttpException(422, json_encode($grade->errors));
        }
    }

    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionDelete ($id) {
        //TODO проверить доступ на удаление оценки

        $grade = GradeRecord::findOne($id);

        if (empty($grade))
            throw new HttpException(404, "Not Found");

        $grade -> delete();

        return 'ok';
    }
}