<?php

namespace app\models\records;

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
 * @property  $group GroupRecord
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

    }


    public function getGroup(){
        return $this->hasOne(GroupRecord::class, ['id' => 'groupId']);
    }
}