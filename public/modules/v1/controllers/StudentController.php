<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\{
    search\StudentSearch, User, Student
};
use app\models\records\{
    GroupRecord, StudyingRecord
};


/**
 * Class StudentController
 * @package app\modules\v1\controllers
 */
class StudentController extends ActiveController
{
    /** @var string Student */
    public $modelClass = Student::class;
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
            'except' => [ 'options', 'search' ]
        ];


        return $behaviors;
    }


    public function actions () {

    }


    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => Student::find()
        ]);
    }


    /**
     * @param $id
     * @throws HttpException
     * @return Student
     */
    public function actionView ($id) {
        $student = Student::findOne($id);

        if (empty($student))
            throw new HttpException(404, "Not Found");

        return $student;
    }


    /**
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $student = new Student();
        if ($student->load(Yii::$app->request->post()) && $student->validate()) {
            $student->save();
            $student->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

            return $student;
        } else {
            throw new HttpException(422, json_encode($student->errors));
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

        $student = $this->actionView($id);

        if (empty($student))
            throw new HttpException(404, "Not Found");

        $student -> delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    public function actionSearch () {
        $studentSearch = new StudentSearch();

        $studentSearch->load(Yii::$app->request->post());

        return $studentSearch->search();
    }


    public function actionTest () {
        return Student::find()
            ->joinWith('group')
            ->andWhere(['group.id' => null])
            ->all();
    }


    public function actionOptions () {
        return 'ok';
    }
}