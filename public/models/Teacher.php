<?php
namespace app\models;

use Yii;
use app\models\records\{
    AcademicYearRecord, AssignedSubjectRecord, SubjectRecord, TeachesRecord
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


    /**
     * @param $condition
     * @return Teacher[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function findAll($condition) {
        return static::findByCondition($condition)->all();
    }


    /**
     * @param $condition
     * @return Teacher
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOne($condition) {
        return static::findByCondition($condition)->one();
    }


    public function rules () {

        $rules = parent::rules();

        $rules[] = ['role', 'default', 'value' => static::ROLE_TEACHER];

        return $rules;
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teaches';
        $fields[] = 'subjects';
        $fields[] = 'assignedSubjects';

        $fields['teachingYears'] = function () {
            $models = AcademicYearRecord::find()
                ->joinWith('semesters.teaches.teacher')
                ->where(['`teaches`.`userId`' => $this->id])
                ->with('semesters')->all();

            $modelsArray = [];
            foreach ($models as $model) {
                $modelsArray[] = $model->toArray([], ['semesters']);
            }

            return $modelsArray;
        };

        return $fields;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['userId' => 'id']);
    }


    public function getSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])->via('teaches');
    }


    public function getAssignedSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])
            ->viaTable(AssignedSubjectRecord::tableName(), ['userId' => 'id']);
    }


    public function getTeachingYears () {
        return $this->hasMany(AcademicYearRecord::class, ['id' => 'yearId']);
    }
}