<?php

namespace app\models\records;

use app\models\Student;
use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

/**
 * Class StudyingRecord
 * @package app\models\records
 *
 * @property  $id        integer
 * @property  $userId    integer
 * @property  $groupId   integer
 * @property  $isActive  integer
 * @property  $createdAt integer
 * @property  $createdBy integer
 * @property  $updatedAt integer
 * @property  $updatedBy integer
 *
 * @property  $group   GroupRecord
 * @property  $student Student
 */
class StudyingRecord extends ActiveRecord
{

    public static function tableName()
    {
        return 'studying';
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
            [['userId', 'groupId'], 'required'],
            [['userId', 'groupId'], 'integer'],
            ['userId', 'validateUserId'],
            ['groupId', 'validateGroupId'],
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'student';
        $fields[] = 'group';

        return $fields;
    }


    public function getStudent () {
        return $this->hasOne(Student::class, ['id' => 'userId']);
    }


    public function getGroup(){
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId']);
    }


    public function validateUserId ($attribute, $params) {
        $student = Student::findOne($this->userId);

        if (empty($student)){
            $this->addError($attribute, Yii::t('app', 'Student does not exists'));
            return false;
        }

        return true;
    }


    public function validateGroupId ($attribute, $params) {
        $group = GroupRecord::findOne($this->groupId);

        if (empty($group)){
            $this->addError($attribute, Yii::t('app', 'Group does not exists'));
            return false;
        }

        return true;
    }
}