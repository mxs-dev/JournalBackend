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
use app\models\records\{
    GradeRecord, LessonRecord, TeachesRecord
};

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

        $students = $teaches -> group -> students;

        $totalLesson = $teaches->getLessons()->where(['type' => LessonRecord::TYPE_TOTAL])->one();

        if (!$totalLesson) {
            $totalLesson = new LessonRecord();
            $totalLesson -> teachesId = $teaches -> id;
            $totalLesson -> date = date('Y-m-d');
            $totalLesson -> type = LessonRecord::TYPE_TOTAL;
            $totalLesson -> weight = 0;
            $totalLesson -> minGradeValue = 55;
            $totalLesson -> maxGradeValue = 100;
            $totalLesson -> description = "Итоговая оценка";

            $totalLesson -> save();
        }

        foreach ($students as $student) {
            $grades = $teaches->getGrades()->where(['`grade`.`userId`' => $student->id])->with('lesson')->all();

            $totalGrade = false;
            $totalGradeValue = 0;

            foreach($grades as $grade) {
                if ($grade -> lesson -> type == LessonRecord::TYPE_TOTAL) {
                    $totalGrade = $grade;
                } else {
                    $totalGradeValue += $grade->value * $grade->lesson->weight;
                }
            }

            if ($totalGrade) {
                $totalGrade -> previousValue = $totalGrade->value;
                $totalGrade -> value = $totalGradeValue;
                $totalGrade -> save();
            } else {
                $totalGrade = new GradeRecord();
                $totalGrade -> lessonId = $totalLesson -> id;
                $totalGrade -> userId   = $student -> id;
                $totalGrade -> attendance = 1;
                $totalGrade -> value = $totalGradeValue;
                if (!$totalGrade -> save())
                    debug($totalGrade->errors);
            }
        }
    }


    public function actionOptions () {
        return 'ok';
    }
}