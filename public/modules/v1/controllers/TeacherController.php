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
 * Class TeacherController
 * @package app\modules\v1\controllers
 */
class TeacherController extends ActiveController
{
    /** @var string Teacher */
    public $modelClass = Teacher::class;
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
     * @param $teacherId
     * @return ActiveDataProvider|ArrayDataProvider
     * @throws HttpException
     */
    public function actionTeaches ($teacherId) {
        $teacher = Teacher::findOne($teacherId);

        if (empty($teacher))
            throw new HttpException(404, "Not Found");

        return new ArrayDataProvider([
            'allModels' => $teacher->teaches
        ]);

        return new ActiveDataProvider([
            'query' => $teacher->getTeaches()
        ]);
    }
}