<?php
namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ User, Teacher, Student };
use app\models\records\{ TeachesRecord };

/**
 * Class SubjectRecord
 * @package app\models\records
 *
 * @property  $id          integer
 * @property  $title       string
 * @property  $description string
 * @property  $createdAt   integer
 * @property  $createdBy   integer
 * @property  $updatedAt   integer
 * @property  $updatedBy   integer
 *
 * @property  $teaches  TeachesRecord[]
 * @property  $teachers Teacher[]
 */
class SubjectRecord extends ActiveRecord
{
    public static function tableName () {
        return 'subject';
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
            [['id', 'title', 'description'], 'required'],
            ['title', 'string', 'max' => 50],
            ['description', 'string']
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teaches';
        $fields[] = 'teachers';

        return $fields;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['subjectId' => 'id']);
    }


    public function getTeachers () {
        return $this->hasMany(Teacher::class, ['id' => 'userId'])->via('teaches');
    }
}