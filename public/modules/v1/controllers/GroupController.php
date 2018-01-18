<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 14.01.2018
 * Time: 13:10
 */

namespace app\modules\v1\controllers;

use app\models\records\GroupRecord;

class GroupController extends \yii\rest\ActiveController
{
    public $modelClass           = GroupRecord::class;
    public $enableCsrfValidation = false;
}