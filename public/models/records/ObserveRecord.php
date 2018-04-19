<?php
namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ User, Student };


/**
 * Class ObserveRecord
 * @package app\models\records
 *
 * @property  $id        integer
 * @property  $userId    integer
 * @property  $childId   integer
 * @property  $createdAt integer
 * @property  $createdBy integer
 * @property  $updatedAt integer
 * @property  $updatedBy integer
 *
 * @property  $user      User
 * @property  $students  Student[]
 */
class ObserveRecord extends ActiveRecord
{

    public static function tableName()
    {
        return 'observe';
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
            [['userId', 'childId'], 'required'],
            ['childId', 'validateChildId']
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'user';
        $fields[] = 'students';

        return $fields;
    }


    public function validateChildId ($attribute, $params) {
        $student = Student::findOne($this->childId);

        if (empty($student)){
            $this->addError($attribute, Yii::t('app', 'Student does not exists'));
            return false;
        }

        return true;
    }


    public function getUser () {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function getStudents () {
        return $this->hasMany(Student::class, ['id' => 'childId']);
    }

}