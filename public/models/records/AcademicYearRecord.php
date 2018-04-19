<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };


/**
 * Class AcademicYearRecord
 * @package app\models\records
 *
 * @property integer $id
 * @property string  $title
 * @property integer $startDate
 * @property integer $endDate
 *
 * @property integer $createdAt
 * @property integer $createdBy
 * @property integer $updatedAt
 * @property integer $updatedBy
 */
class AcademicYearRecord extends ActiveRecord
{
    public static function tableName() {
        return 'academic_year';
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
            [['title', 'startDate', 'endDate'], 'required'],
            [['startDate', 'endDate'], 'date']
        ];
    }


    public function extraFields()
    {
        $extraFields = parent::extraFields();

        $extraFields[] = 'semesters';

        return $extraFields;
    }


    public function getSemesters () {
        return $this->hasMany(SemesterRecord::class, ['yearId' => 'id']);
    }
}