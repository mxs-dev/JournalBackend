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
 * @property  $id         integer
 * @property  $teachesId  integer
 * @property  $date       integer
 * @property  $type       integer
 * @property  $description string
 * @property  $createdAt  integer
 * @property  $createdBy  integer
 * @property  $updatedAt  integer
 * @property  $updatedBy  integer
 */
class LessonRecord extends ActiveRecord
{
    const TYPE_LECTURE  = 1;
    const TYPE_PRACTICE = 2;


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


    public function rules () {
        return [
            [['id', 'teachesId', 'date'], 'required'],
            [['date'], 'integer'],
            [['description'], 'string', 'max' => 255]
        ];
    }


    public function getTeaches () {
        return $this->hasOne(TeachesRecord::class, ['id' => 'teachesId']);
    }


    public function getTeacher () {
        return $this->hasOne(User::class, ['id' => 'userId'])->via('teaches');
    }

}