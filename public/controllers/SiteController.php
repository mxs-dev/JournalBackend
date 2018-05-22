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
		$user = \app\models\User::findByEmailConfirmToken('xbcArq-9TvK4CbfFqkqPV_gtEkxV_lNC_1526989915');

		debug($user);
    }
}
