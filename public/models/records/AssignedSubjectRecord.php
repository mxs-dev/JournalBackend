<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ Teacher };
use app\models\records\ { LessonRecord };


/**
 * Class AssignedSubjectRecord
 * @package app\models\records\
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $subjectId
 * @property integer $createdAt
 * @property integer $createdBy
 * @property integer $updatedAt
 * @property integer $updatedBy
 */
class AssignedSubjectRecord extends ActiveRecord
{
    public static function tableName () {
        return 'assigned_subject';
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
            [['userId', 'subjectId'], 'required'],
        ];
    }


    public function afterValidate ()
    {
        parent::afterValidate();

        $modelExists = static::find()
        ->andWhere(['userId' => $this->userId])
        ->andWhere(['subjectId' => $this->subjectId])
        ->one();

        if ($modelExists) {
            $this->addError('subjectId', 'Model exists');
            return false;
        }

        return true;
    }


    public function getTeacher () {
        return $this->hasOne(Teacher::class, ['userId' => 'id']);
    }


    public function getSubject () {
        return $this->hasOne(SubjectRecord::class, ['subjectId' => 'id']);
    }
}