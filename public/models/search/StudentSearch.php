<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 30.03.2018
 * Time: 21:44
 */

namespace app\models\search;


use app\models\Student;
use yii\base\Model;

class StudentSearch extends Model
{
    public $name       = "";
    public $surname    = "";
    public $patronymic = "";
    public $notInGroup = false;

    public function rules () {
        return [
            [['name', 'surname', 'patronymic'], 'string'],
            ['notInGroup', 'boolean'],
        ];
    }


    public function search () {
        if ($this->notInGroup) {
            return Student::find()->joinWith('group')
                ->andWhere(['group.id' => null])
                ->andWhere(['like', 'name',       $this->name])
                ->andWhere(['like', 'surname',    $this->surname])
                ->andWhere(['like', 'patronymic', $this->patronymic])
                ->all();
        } else {
           /* return Student::find()
                ->andWhere(['like', 'name',       $this->name])
                ->andWhere(['like', 'surname',    $this->surname])
                ->andWhere(['like', 'patronymic', $this->patronymic])
                ->all();*/

           return ['WTF', 'hm'];
        }
    }
}