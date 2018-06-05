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
		debug(Yii::$app->security->generatePasswordHash('teacher'));
    }
}
