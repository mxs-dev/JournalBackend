<?php
namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ User, Student, Teacher };
use app\models\records\{ LessonRecord, TeachesRecord };

/**
 * Class GradeRecord
 * @package app\models\records
 *
 * @property  $id         integer
 * @property  $userId     integer
 * @property  $lessonId   integer
 * @property  $attendance integer
 * @property  $value      integer
 * @property  $previousValue integer
 * @property  $createdAt  integer
 * @property  $createdBy  integer
 * @property  $updatedAt  integer
 * @property  $updatedBy  integer
 *
 * @property  $lesson   LessonRecord
 * @property  $teaches  TeachesRecord
 * @property  $teacher  Teacher
 */
class GradeRecord extends ActiveRecord
{
    const ATT_YES     =  1;
    const ATT_NO      = -1;
    const ATT_NO_RESP =  0;


    public static function tableName()
    {
        return 'grade';
    }


    public function behaviors()
    {
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
            [['userId', 'lessonId', 'attendance'], 'required'],
            [['userId', 'lessonId', 'attendance', 'value'], 'integer'],

            [['userId'], 'validateUserAndLesson'],
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teacher';
        $fields[] = 'teaches';
        $fields[] = 'lesson';

        return $fields;
    }


    public function validateUserAndLesson ($attribute, $params) {
        // Проверка что существует студент с таким ID, который учится в группе с таким ID.
        $student = Student::find()
            ->joinWith('studying.group.teaches.lessons', true,'INNER JOIN')
            ->where(['user.id' => $this->userId, 'lesson.id' => $this->lessonId])
            ->one();

        if (!empty($student)){
            return true;
        }

        $this->addError($attribute, Yii::t('app', 'User cannot have grade on this lesson'));
        return false;
    }


    public function getLesson () {
        return $this->hasOne(LessonRecord::class, ['id' => 'lessonId']);
    }


    public function getTeaches () {
        return $this->hasOne(TeachesRecord::class, ['id' => 'teachesId'])->via('lesson');
    }


    public function getTeacher () {
        return $this->hasOne(Teacher::class, ['id' => 'userId'])->via('teaches');
    }

}