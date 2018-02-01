<?php
namespace app\models;

use app\models\records\{
    SubjectRecord, TeachesRecord
};


/**
 * Class Teacher
 * @package app\models
 *
 * @property  $teaches  TeachesRecord[]
 * @property  $subjects SubjectRecord[]
 */
class Teacher extends User
{
    public static function find() {
        return parent::find()->andWhere(['role' => static::ROLE_TEACHER]);
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

        $fields[] = 'teaches';
        $fields[] = 'subjects';

        return $fields;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['userId' => 'id']);
    }


    public function getSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])->via('teaches');
    }
}