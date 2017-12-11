<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 10.12.2017
 * Time: 21:24
 */

namespace app\modules\v1;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\v1\controllers';

    public function init() {
        parent::init();

        \Yii::$app->user->enableSession = false;
    }
}