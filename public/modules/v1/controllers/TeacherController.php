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
use app\models\records\{
    AssignedSubjectRecord, SubjectRecord, TeachesRecord
};


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

        $behaviors['verbs'] = [
            'class'   => VerbFilter::class,
            'actions' => [
                'index'  => ['GET'],
                'view'   => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'add-assigned-subject'    => ['GET', 'POST'],
                'remove-assigned-subject' => ['DELETE']
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
     * @param $teacherId
     * @param $subjectId
     * @return string
     * @throws HttpException
     */
    public function actionAddAssignedSubject ($teacherId, $subjectId) {
        $assignedSubject = new AssignedSubjectRecord();
        $assignedSubject->userId    = $teacherId;
        $assignedSubject->subjectId = $subjectId;

        if ($assignedSubject->validate()) {
            $assignedSubject->save();
            $assignedSubject->refresh();

            Yii::$app->getResponse()->setStatusCode(201);
            return "ok";
        }

        throw new HttpException(422, json_encode($assignedSubject->getErrors()));
    }


    /**
     * @param $teacherId
     * @param $subjectId
     * @return string
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveAssignedSubject ($teacherId, $subjectId) {
        $assignedSubject = AssignedSubjectRecord::find()
            ->where(['userId' => $teacherId])
            ->andWhere(['subjectId' => $subjectId])
            ->one();

        if (empty($assignedSubject))
            throw new HttpException(404, "Not Found");

        $assignedSubject->delete();
        Yii::$app->getResponse()->setStatusCode(204);

        return "ok";
    }


    public function actionGetTeachesToday () {

    }


    public function actionGetAllTeaches () {

        $teacher = Teacher::find()
            ->one();

        return $teacher;
    }


    /**
     * @return ActiveDataProvider
     */
    public function actionIndex () {
        return new ActiveDataProvider([
            'query' => Teacher::find()
        ]);
    }


    /**
     * @param $id
     * @return Teacher|null
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView ($id) {
        $teacher = Teacher::findOne($id);

        if (empty($teacher))
            throw new HttpException(404, "NotFound");

        return $teacher;
    }


    /**
     * @return Teacher
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $teacher = new Teacher();

        if ($teacher->load(Yii::$app->request->post()) && $teacher->validate()){
            $teacher->save();
            $teacher->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

            return $teacher;
        }

        throw new HttpException(422, json_encode($teacher->getErrors()));
    }


    /**
     * @param $id
     * @return Teacher | null
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $teacher = Teacher::findOne($id);

        if (empty($teacher))
            throw new HttpException(404, "Not Found");

        if ($teacher->load(Yii::$app->request->post()) && $teacher->validate()){
            $teacher->save();
            $teacher->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

            return $teacher;
        }

        throw new HttpException(422, json_encode($teacher->getErrors()));
    }


    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete ($id) {
        $teacher = Teacher::findOne($id);

        if (empty($teacher))
            throw new HttpException(404, "Not Found");

        $teacher->delete();
        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    public function actionOptions () {
        return 'ok';
    }
}