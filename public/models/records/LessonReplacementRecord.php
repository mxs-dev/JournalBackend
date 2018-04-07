<?php

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\{ Teacher };

/**
 * Class SubjectReplacement
 * @package app\models\records
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $lessonId
 * @property integer $createdAt
 * @property integer $createdBy
 * @property integer $updatedAt
 * @property integer $updatedBy
 */
class LessonReplacementRecord extends ActiveRecord
{
    public static function tableName () {
        return 'lesson_replacement';
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
            [['userId', 'lessonId'], 'required'],
            ['userId', 'validateUserId'],
            ['lessonId', 'validateLessonId'],
        ];
    }


    public function validateUserId ($attribute, $params) {
        $user = Teacher::find()->andWhere(['id' => $this->userId])->one();

        //TODO добавить логику проверки того, что преподаватель имеет право вести данную замену.

        return true;
    }


    public function validateLessonId ($attribute, $params) {
        return true;
    }


    public function getLesson () {
        return $this->hasOne(LessonRecord::class, ['id' => 'lessonId']);
    }


    public function getTeacher () {
        return $this->hasOne(Teacher::class, ['id' => 'userId']);
    }
}