<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };


/**
 * Class SemesterRecord
 * @package app\models\records
 *
 * @property integer $id
 * @property integer $yearId
 * @property integer $number
 * @property integer $startDate
 * @property integer $endDate
 * @property integer $createdAt
 * @property integer $createdBy
 * @property integer $updatedAt
 * @property integer $updatedBy
 */
class SemesterRecord extends ActiveRecord
{
    public static function tableName () {
        return 'semester';
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
            [['yearId', 'number', 'startDate', 'endDate'], 'required'],
        ];
    }


    public function getYear () {
        return $this->hasOne(AcademicYearRecord::class, ['yearId' => 'id']);
    }
}