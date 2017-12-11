<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 10.12.2017
 * Time: 21:30
 */

namespace app\modules\v1\controllers;

use yii\filters\auth\CompositeAuth;
use app\filters\auth\HttpBearerAuth;
use yii\web\IdentityInterface;

class UserController extends \yii\web\Controller
{
    public function behaviors () {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ],
            'except' => ['']
        ];
        //$behaviors['authenticator']['except'] = [''];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                '123' => ['get', 'post', 'update']
            ]
        ];


    }
}