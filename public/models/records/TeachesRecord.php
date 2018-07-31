<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 18.01.2018
 * Time: 13:05
 */

namespace app\models\records;

use Yii;
use yii\db\{ ActiveQuery, Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };
use app\models\{ User, Student, Teacher };

/**
 * Class TeachesRecord
 * @package app\models\records
 *
 * @property  integer $id
 * @property  integer $userId
 * @property  integer $subjectId
 * @property  integer $groupId
 * @property  integer $hoursCount
 * @property  integer $createdAt
 * @property  integer $createdBy
 * @property  integer $updatedAt
 * @property  integer $updatedBy
 * @property  GroupRecord $group
 * @property  LessonRecord[] $lessons
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
            [['userId', 'subjectId', 'groupId', 'semesterId', 'hoursCount'], 'required'],
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
        $fields[] = 'semester';

        return $fields;
    }


    /**
     * @param $attribute
     * @param $params
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
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
        $subject = SubjectRecord::findOne($this->subjectId);

        if (empty($subject)){
            $this->addError($attribute, Yii::t('app', 'Subject does not exists'));
            return false;
        }

        return true;
    }


    public function getSemester (): ActiveQuery {
        return $this->hasOne(SemesterRecord::class, ['id' => 'semesterId']);
    }


    public function getTeacher (): ActiveQuery {
        return $this->hasOne(Teacher::class,  ['id' => 'userId']);
    }


    public function getLessons (): ActiveQuery {
        return $this->hasMany(LessonRecord::class, ['teachesId' => 'id']);
    }

    public function getGrades (): ActiveQuery {
        return $this->hasMany(GradeRecord::class, ['lessonId' => 'id'])->via('lessons');
    }


    public function getGroup (): ActiveQuery {
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId']);
    }


    public function getSubject (): ActiveQuery {
        return $this->hasOne(SubjectRecord::class, ['id' => 'subjectId']);
    }
}