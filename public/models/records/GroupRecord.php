<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 14.01.2018
 * Time: 13:11
 */

namespace app\models\records;

use yii\db\{ Expression, ActiveRecord };
use yii\behaviors\{ TimestampBehavior, BlameableBehavior };

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
}