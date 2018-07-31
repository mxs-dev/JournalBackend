<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 27.01.2018
 * Time: 16:01
 */

namespace app\models;

use Yii;
use app\models\records\{
    AcademicYearRecord, GroupRecord, StudyingRecord, TeachesRecord
};
use yii\db\ActiveQuery;


/**
 * Class Student
 * @package app\models
 *
 * @property  StudyingRecord $studying
 * @property  GroupRecord $group
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


    public function rules () {

        $rules = parent::rules();

        $rules[] = ['role', 'default', 'value' => static::ROLE_STUDENT];

        return $rules;
    }


    public function extraFields() {
        $fields = parent::extraFields();

        $fields[] = 'studying';
        $fields[] = 'group';

        $fields['academicPerformance'] = function () {
            return $this->getAcademicPerformance();
        };

        return $fields;
    }


    public function getStudying () {
        return $this->hasOne(StudyingRecord::class, ['userId' => 'id']);
    }


    public function getGroup (): ActiveQuery {
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId'])->via('studying');
    }

    public function getAcademicPerformance () {
//        $models = AcademicYearRecord::find()
//            ->joinWith(['semesters.teaches.lessons.grades'])
//            ->andWhere([
//                '`grade`.`userId`' => $this->id
//            ])
//            ->with('semesters.teaches.lessons.grades', 'semesters.teaches.subject')
//            ->all();
//        $modelsArray = [];
//
//        foreach ($models as $model) {
//            $modelsArray[] = $model->toArray([], ['semesters.teaches.lessons.grades', 'semesters.teaches.subject']);
//        }
//
//        return $modelsArray;



        $teachingYears = AcademicYearRecord::find()
            ->joinWith(['semesters.teaches.lessons.grades'])
            ->andWhere(['`teaches`.`groupId`' => $this->group->id])
            ->with('semesters')
            ->all();

        $academicPerformance = [];

        foreach ($teachingYears as $year) {
            $tempArr = $year->toArray([], ['semesters']);

            foreach($tempArr['semesters'] as $key => $semester) {

                $scheduleItems = TeachesRecord::find()
                    ->where(['groupId' => $this->group->id])
                    ->andWhere(['semesterId' => $semester['id']])
                    ->with(['lessons.grades', 'subject'])
                    ->all();

                foreach($scheduleItems as $item) {
                    $tempArr['semesters'][$key]['teaches'][] = $item->toArray([], ['lessons.grades', 'subject']);
                }
            }

            $academicPerformance[] = $tempArr;
        }

        return $academicPerformance;
    }
}