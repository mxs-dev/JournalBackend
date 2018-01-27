<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 18.01.2018
 * Time: 13:04
 */

namespace app\models\records;

use Yii;
use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

/**
 * Class SubjectRecord
 * @package app\models\records
 *
 * @property  $id          integer
 * @property  $title       string
 * @property  $description string
 * @property  $createdAt   integer
 * @property  $createdBy   integer
 * @property  $updatedAt   integer
 * @property  $updatedBy   integer
 */
class SubjectRecord extends ActiveRecord
{
    public static function tableName () {
        return 'subject';
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
            [['id', 'title', 'description'], 'required'],
            ['title', 'string', 'max' => 50],
            ['description', 'string']
        ];
    }
}