<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\{ Teacher, Student };


/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }


    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionTestSse ()
    {
        return $this->render('test-sse');
    }


    public function actionTest(){

        $student = Student::find()->joinWith('studying.group.teaches.lessons', true,'INNER JOIN')->where(['group.id' => 4, 'lesson.id' => 1])->one();
        echo json_encode($student->toArray());
        return $this->render('index');


        /** @var Student $student */
        /*
        $student = Student::find()->joinWith('studying.group')->with('studying.group')->where(['user.id' => 3])->one();

        if (empty($student)){
            echo json_encode(['null' => 'null']);
            return $this->render('index');
        }

        echo json_encode($student->studying);
        //echo print_r($student);
        //echo json_encode($student->toArray([], ['subjects']));
        return $this->render('index');*/
    }
}
