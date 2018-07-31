<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\User;

/**
 * Class LessonRecord
 * @package app\models\records
 *
 * @property  $id             integer
 * @property  $teachesId      integer
 * @property  $date           integer
 * @property  $type           integer
 * @property  $description    string
 * @property  $weight         integer
 * @property  $minGradeValue  integer
 * @property  $maxGradeValue  integer
 * @property  $createdAt      integer
 * @property  $createdBy      integer
 * @property  $updatedAt      integer
 * @property  $updatedBy      integer
 *
 * @property  SubjectRecord $subject
 * @property  TeachesRecord $teaches
 * @property  Teacher       $teacher
 * @property  GradeRecord[] $grades
 */
class LessonRecord extends ActiveRecord
{
    const TYPE_LECTURE  = 1;
    const TYPE_PRACTICE = 2;
    const TYPE_CONTROL  = 3;
    const TYPE_EXAM     = 4;
    const TYPE_TOTAL    = 99;


    public static function tableName () {
        return 'lesson';
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


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teaches';
        $fields[] = 'teacher';
        $fields[] = 'grades';

        return $fields;
    }


    public function rules () {
        return [
            [['teachesId', 'date'], 'required'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['description'], 'string', 'max' => 255],
            ['weight', 'number'],
            ['type', 'number'],
            [['minGradeValue', 'maxGradeValue'], 'safe'],
            ['teachesId', 'validateTeachesId']
        ];
    }


    public function getTeaches () {
        return $this->hasOne(TeachesRecord::class, ['id' => 'teachesId']);
    }


    public function getSubject () {
        return $this->hasOne(SubjectRecord::class, ['id' => 'subjectId'])->via('teaches');
    }


    public function getTeacher () {
        return $this->hasOne(User::class, ['id' => 'userId'])->via('teaches');
    }


    public function getGrades () {
        return $this->hasMany(GradeRecord::class, ['lessonId' => 'id']);
    }


    public function validateTeachesId ($attribute, $params) {
        $teaches = TeachesRecord::find()->where(['id' => $this->teachesId])->with('teacher')->one();

        if (empty($teaches)){
            $this->addError($attribute, Yii::t('app', 'Teaches not found'));

            return false;
        }

        return true;
    }
}