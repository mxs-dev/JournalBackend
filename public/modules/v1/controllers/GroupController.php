<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\{ User, Student };
use app\models\records\{
    GroupRecord, StudyingRecord
};


/**
 * Class GroupController
 * @package app\modules\v1\controllers
 */
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


    /**
     * @param $groupId integer
     * @return ActiveDataProvider
     */
    public function actionStudents ($groupId) {
        return new ActiveDataProvider([
            'query' => Student::find()->joinWith('group')->where(['group.id' => $groupId]),
        ]);
    }


    /**
    * @param $groupId
    * @param $studentId
    * @return string
    * @throws HttpException
    * @throws \Exception
    * @throws \Throwable
    * @throws \yii\db\StaleObjectException
    */
    public function actionAddStudent($groupId, $studentId) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = new StudyingRecord();

        $model->groupId = $groupId;
        $model->userId  = $studentId;

        if ($model->validate()) {
            Yii::$app->getResponse()->setStatusCode(201);

            $model->save();

            return 'ok';
        } else {

            throw new HttpException(422, json_encode($model->errors));
        }
    }


    /**
     * @param $groupId
     * @param $studentId
     * @return string
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveStudent($groupId, $studentId) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = StudyingRecord::find()->where([
            'groupId' => $groupId,
            'userId' => $studentId
        ]) -> one();

        if (empty($model))
            throw new HttpException(404, 'Not Found');

        $model->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    /**
     * @return ActiveDataProvider
     */
    public function actionIndex () {
        return new ActiveDataProvider([
           'query' => GroupRecord::find()
        ]);
    }


    /**
     * @param $id
     * @return null|GroupRecord
     * @throws HttpException
     */
    public function actionView ($id) {
        $group = GroupRecord::findOne($id);

        if (empty($group))
            throw new HttpException(404, "Not Found");

        return $group;
    }


    /**
     * @param $id
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionDelete ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $group = $this->actionView($id);

        if (empty($group))
            throw new HttpException(404, "Not Found");

        $group->delete();

        Yii::$app->getResponse()->setStatusCode(204);
        return "ok";
    }


    /**
     * @return GroupRecord
     * @throws HttpException
     */
    public function actionCreate () {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $group = new GroupRecord();

        if ($group->load(Yii::$app->request->post()) && $group->validate()){
            $group->save();
            $group->refresh();

            Yii::$app->getResponse()->setStatusCode(201);

           return $group;
        } else {

            throw new HttpException(422, json_encode($group->errors));
        }
    }


    /**
     * @param $id
     * @return null|GroupRecord
     * @throws HttpException
     */
    public function actionUpdate ($id) {
        if (!Yii::$app->user->can(User::ROLE_MODER))
            throw new HttpException(401, "Access denied");

        $model = GroupRecord::findOne($id);

        if (empty($model))
            throw new HttpException(404, "Not Found");

        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->save();

            return $model;
        }

        throw new HttpException(422, json_encode($model->errors));
    }


    public function actionOptions () {
        return 'ok';
    }
}