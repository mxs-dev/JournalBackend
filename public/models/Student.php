<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 27.01.2018
 * Time: 16:01
 */

namespace app\models;

use app\models\records\{ GroupRecord, StudyingRecord };


/**
 * Class Student
 * @package app\models
 *
 * @property  $studying StudyingRecord
 * @property  $group    GroupRecord
 */
class Student extends User
{
    public static function find() {
        return parent::find()->andWhere(['role' => static::ROLE_STUDENT]);
    }


    public static function findAll($condition) {
        return static::findByCondition($condition)->all();
    }


    public static function findOne($condition) {
        return static::findByCondition($condition)->one();
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields['studying'] = function () {
            return $this->studying;
        };

        $fields['group'] = function () {
            return $this->group;
        };

        return $fields;
    }


    public function getStudying () {
        return $this->hasOne(StudyingRecord::class, ['userId' => 'id']);
    }


    public function getGroup () {
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId'])->via('studying');
    }
}