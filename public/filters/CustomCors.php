<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 18.01.2018
 * Time: 22:03
 */

namespace app\filters;

use Yii;
use yii\filters\Cors;

class CustomCors extends Cors
{
    
    /**
     * @param $action
     * @return bool
     * @throws \yii\base\ExitException
     */
    public function beforeAction($action)
    {
        $request  = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        parent::beforeAction($action);

        if ($request->getMethod() === 'OPTIONS'){
            $response->getHeaders()->set('Allow', 'POST GET PUT DELETE HEAD');
            $response->setStatusCode(200);
            $response->data = "OK";
            Yii::$app->end();
        }

        return true;
    }
}