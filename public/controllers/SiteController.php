<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\Student;


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

        /** @var Student $student */
        $student = Student::findOne(3);
        if (empty($student)){
            echo json_encode(['null' => 'null']);
            return $this->render('index');
        }


        echo json_encode($student->toArray([], ['studying', 'group']));
        return $this->render('index');
    }
}
