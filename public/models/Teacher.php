<?php
namespace app\models;

use app\models\records\{
    SubjectRecord, TeachesRecord
};


/**
 * Class Teacher
 * @package app\models
 *
 * @property  $teaches  TeachesRecord | array
 * @property  $subjects SubjectRecord | array
 */
class Teacher extends User
{
    const CURRENT_ROLE = User::ROLE_TEACHER;


    public static function find() {
        return parent::find()->andWhere(['role' => static::CURRENT_ROLE]);
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

        $fields['teaches'] = function () {
            return $this->teaches;
        };

        $fields['subjects'] = function () {
            return $this->subjects;
        };

        return $fields;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['userId' => 'id']);
    }


    public function getSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])->via('teaches');
    }
}