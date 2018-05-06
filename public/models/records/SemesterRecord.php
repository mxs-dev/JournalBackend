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
    /** @var AcademicYearRecord */
    protected $year;

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
            ['yearId', 'validateYearId'],
            ['startDate', 'validateStartDate'],
            ['endDate', 'validateEndDate']
        ];
    }


    public function extraFields () {
        $fields = parent::extraFields();

        $fields[] = 'year';
        $fields[] = 'teaches';

        return $fields;
    }


    public function getYear () {
        return $this->hasOne(AcademicYearRecord::class, ['id' => 'yearId']);
    }


    public function validateYearId ($attribute, $params) {
        $this->year = AcademicYearRecord::findOne($this->yearId);

        if (empty($this->year)) {
            $this->addError($attribute, 'Academic year does not exists');
            return false;
        }

        return true;
    }


    public function validateStartDate ($attribute, $params) {
        if (empty($this->year)) {
            $this->addError($attribute, 'Academic year does not exists');
            return false;
        }

        $date1 = (new \DateTime($this->startDate))->getTimestamp();
        $date2 = (new \DateTime($this->year->startDate))->getTimestamp();

        if ($date1 < $date2) {
            $this->addError($attribute,'Semester start date cannot be earlier than academic year starts.');
            return false;
        }

        return true;
    }


    public function validateEndDate ($attribute, $params) {
        if (empty($this->year)) {
            $this->addError($attribute, 'Academic year does not exists');
            return false;
        }

        $date1 = (new \DateTime($this->endDate))->getTimestamp();
        $date2 = (new \DateTime($this->year->endDate))->getTimestamp();

        if ($date1 > $date2) {
            $this->addError($attribute, 'Semester end date cannot be later than academic year ends.');
            return false;
        }

        return true;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['semesterId' => 'id']);
    }
}