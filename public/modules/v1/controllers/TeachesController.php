<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\{ HttpException };
use yii\rest\{ ActiveController };
use yii\data\{ ActiveDataProvider, ArrayDataProvider };
use yii\filters\{ VerbFilter };
use yii\filters\auth\{ CompositeAuth, HttpBearerAuth};

use app\filters\CustomCors;
use app\models\records\{ GroupRecord, GradeRecord, LessonRecord, TeachesRecord };

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


    /**
     * @param $id
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCalculateTotalGrades ($id) {
        $teaches = $this->actionView($id);

        // Получение или создание записи о выставлении итоговой оценки
        $totalLesson = $teaches->getLessons()
            ->where(['type' => LessonRecord::TYPE_TOTAL])
            ->with('grades')
            ->one()
            ?? $this->createTotalLesson($teaches);

        // Получение всех оценок студентов группы
        /** @var GradeRecord[] $grades */
        $grades = $teaches->getGrades()
            ->joinWith('lesson')
            ->andWhere(['!=', '`lesson`.`type`', LessonRecord::TYPE_TOTAL])
            ->with('lesson')
            ->all();


        // Рассчет итоговых оценок с учетов весовых коэффициентов
        $finalGrades = [];
        foreach ($grades as $grade) {
            if (empty($finalGrades[$grade->userId]))
                $finalGrades[$grade->userId] = 0;

            if ($grade->attendance > 0) {
                $finalGrades[$grade->userId] += $grade->value * $grade->lesson->weight;
            }
        }

        // Сохранение итоговых оценок
        if ($totalLesson->isNewRecord) {
            $totalLesson -> save();

            foreach ($finalGrades as $userId => $value) {
                $this->createGrade($totalLesson, $value, $userId);
            }
        } else {
            // Changing values in old TotalGrades
            foreach ($totalLesson->grades as $grade) {
                $finalGradeValue = $finalGrades[$grade->userId];

                if ($finalGradeValue !== $grade -> value) {
                    $grade -> previousValue = $grade -> value;
                    $grade -> value = round($finalGradeValue);
                    $grade -> save();
                }
            }
        }
    }


    public function actionCreate () {
        $model = new TeachesRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->response->setStatusCode(201);
            return $model;
        }

        throw new HttpException(422, json_encode($model->errors));
    }


    public function actionOptions () {
        return 'ok';
    }


    protected function createTotalLesson (TeachesRecord $scheduleItem, $minGradeValue = 55, $maxGradeValue = 100): LessonRecord {
        $totalLesson = new LessonRecord();

        $totalLesson -> teachesId = $scheduleItem -> id;
        $totalLesson -> date = date('Y-m-d');
        $totalLesson -> type = LessonRecord::TYPE_TOTAL;
        $totalLesson -> weight = 0;
        $totalLesson -> minGradeValue = $minGradeValue;
        $totalLesson -> maxGradeValue = $maxGradeValue;
        $totalLesson -> description = "TOTAL";

        return $totalLesson;
    }

    protected function createGrade (LessonRecord $lesson, $value, $userId) {
        $grade = new GradeRecord ();

        $grade -> userId     = $userId;
        $grade -> lessonId   = $lesson -> id;
        $grade -> attendance = 1;
        $grade -> value      = round($value);

        $grade -> save();
    }
}