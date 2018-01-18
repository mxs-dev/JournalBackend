<?php
namespace app\models\records;

use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };


/**
 * Class GradeRecord
 * @package app\models\records
 *
 * @property  $id         integer
 * @property  $userId     integer
 * @property  $lessonId   integer
 * @property  $attendance integer
 * @property  $value      integer
 * @property  $createdAt  integer
 * @property  $createdBy  integer
 * @property  $updatedAt  integer
 * @property  $updatedBy  integer
 */
class GradeRecord extends ActiveRecord
{
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
}