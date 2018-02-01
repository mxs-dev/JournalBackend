<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 18.01.2018
 * Time: 13:05
 */

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ User, Student, Teacher };

/**
 * Class TeachesRecord
 * @package app\models\records
 *
 * @property  $id        integer
 * @property  $userId    integer
 * @property  $subjectId integer
 * @property  $groupId   integer
 * @property  $createdAt integer
 * @property  $createdBy integer
 * @property  $updatedAt integer
 * @property  $updatedBy integer
 */
class TeachesRecord extends ActiveRecord
{

    public static function tableName () {
        return 'teaches';
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
            [['userId', 'subjectId', 'groupId'], 'required'],
            ['userId',    'validateUserId'],
            ['groupId',   'validateGroupId'],
            ['subjectId', 'validateSubjectId']
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teacher';
        $fields[] = 'lessons';
        $fields[] = 'group';
        $fields[] = 'subject';

        return $fields;
    }


    public function validateUserId ($attribute, $params) {
        $teacher = Teacher::findOne($this->userId);

        if (empty($teacher)){
            $this->addError($attribute, Yii::t('app', 'Teacher does not exits'));
            return false;
        }

        return true;
    }


    public function validateGroupId ($attribute, $params) {
        $group = GroupRecord::findOne($this->groupId);

        if (empty($group)){
            $this->addError($attribute, Yii::t('app', 'Group does not exits'));
            return false;
        }

        return true;
    }


    public function validateSubjectId ($attribute, $params) {
        $group = SubjectRecord::findOne($this->subjectId);

        //TODO добавить проверку на то, что преподаватель имеет право читать данный предмет.

        if (empty($subject)){
            $this->addError($attribute, Yii::t('app', 'Subject does not exists'));
            return false;
        }

        return true;
    }


    public function getTeacher () {
        return $this->hasOne(Teacher::class,  ['id' => 'userId']);
    }


    public function getLessons () {
        return $this->hasMany(LessonRecord::class, ['teachesId' => 'id']);
    }


    public function getGroup () {
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId']);
    }


    public function getSubject () {
        return $this->hasOne(SubjectRecord::class, ['id' => 'subjectId']);
    }
}