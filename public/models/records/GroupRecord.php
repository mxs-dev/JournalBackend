<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 14.01.2018
 * Time: 13:11
 */

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

use app\models\User;
use app\models\records\{ TeachesRecord };

/**
 * Class GroupRecord
 * @package app\models\records
 *
 * @property  $id integer
 * @property  $title string
 * @property  $course integer
 * @property  $createdAt integer
 * @property  $createdBy integer
 * @property  $updatedAt integer
 * @property  $updatedBy integer
 */
class GroupRecord extends ActiveRecord
{

    public static function tableName()
    {
        return 'group';
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
            [['title', 'course'], 'required'],
            ['title', 'string', 'max' => 50],
            ['title', 'validateTitle']
        ];
    }


    public function getStudying () {
        return $this->hasMany(StudyingRecord::class, ['groupId' => 'id']);
    }


    public function getStudents () {
        return $this->hasMany(User::class, ['id' => 'userId'])->via('studying');
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['groupId' => 'id']);
    }


    public function validateTitle ($attribute, $params) {
        $group = GroupRecord::find()->where(['title' => $this->title])->one();

        if (empty($group)) {
            return true;
        }

        $this->addError($attribute, Yii::t('app', 'Group title should be unique'));
        return false;
    }
}