<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\User;


/**
 * Class GroupRecord
 * @package app\models\records
 *
 * @property  $id integer
 * @property  $title string
 * @property  $course integer
 * @property  $createdAt integer
 * @property  $createdBy integer
 * @property  $updatedAt integer
 * @property  $updatedBy integer
 *
 * @property  $students Student[]
 */
class GroupRecord extends ActiveRecord
{

    public static function tableName()
    {
        return 'group';
    }


    public function behaviors () {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdBy', 'updatedBy'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedBy']
                ]
            ]
        ];
    }


    public function rules () {
        return [
            [['title', 'course'], 'required'],
            ['title', 'string', 'max' => 50],
            ['title', 'unique']
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'students';
        $fields[] = 'studying.students';
        $fields[] = 'teaches';
        $fields['studyingYears'] = function () {
            $models = AcademicYearRecord::find()
                ->joinWith('semesters.teaches.group')
                ->where(['`teaches`.`groupId`' => $this->id])
                ->with('semesters')->all();

            $modelsArray = [];
            foreach ($models as $model) {
                $modelsArray[] = $model->toArray([], ['semesters']);
            }

            return $modelsArray;
        };

        return $fields;
    }


    public function getStudyingYears () {

    }


    public function getStudying () {
        return $this->hasMany(StudyingRecord::class, ['groupId' => 'id']);
    }


    public function getStudents () {
        return $this->hasMany(User::class, ['id' => 'userId'])->via('studying');
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['groupId' => 'id']);
    }
}