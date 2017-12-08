<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 06.11.2017
 * Time: 14:28
 */

namespace app\models\records;


use app\models\iObjectAsArray;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * Class UserRecord
 * @package app\models\records
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property string $gender
 * @property string $type
 * @property string $authKey
 * @property string $accessToken
 * @property integer $createdAt
 * @property integer $createdBy
 */
class UserRecord extends ActiveRecord implements IdentityInterface, iObjectAsArray
{
    protected static $users = [];

    public static function tableName () {
        return 'user';
    }

    public static function findIdentity($id)
    {
        if (isset(static::$users[$id])){
            return static::$users[$id];
        } else {
            static::$users[$id] = static::findOne($id);
            return  static::$users[$id];
        }
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (static::$users as $user){
            if ($user->accessToken == $token){
                return $user;
            }
        }

        $user = static::findOne(['accessToken' => $token]);

        if ($user){
            static::$users[] = $user;
        }

        return $user;
    }

    public static function findByUsername($username)
    {
        foreach(static::$users as $user){
            if ($user->username == $username){
                return $user;
            }
        }

        $user = static::findOne(['username' => $username]);

        if ($user) {
            $users[] = $user;
        }

        return $user;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt']
                ]
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdBy']
                ]
            ]
        ];
    }

    public function beforeSave($insert){
        if (parent::beforeSave($insert)){
            if ($this->isNewRecord){
                $this->accessToken = \Yii::$app->security->generateRandomString();
            }
            return true;
        }

        return false;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey == $authKey;
    }

    public function getId(){
        return $this->id;
    }

    public function getAuthKey(){
        return $this->authKey;
    }

    public function getAsArray($withAccessToken = false){
        $arr = [
            'id'           => $this->id,
            'username'     => $this->username,
            'createdAt'    => $this->createdAt,
            'email'        => $this->email,
            'name'         => $this->name,
            'surname'      => $this->surname,
            'patronymic'   => $this->patronymic,
            //'pictureSmall' => $this->getMainImage()->getUrl([30, null]),
            //'pictureMiddle'=> $this->getMainImage()->getUrl([200, null]),
            //'pictureBig'   => $this->getMainImage()->getUrl()
        ];

        if ($withAccessToken){
            $arr['accessToken'] = $this->accessToken;
        }

        return $arr;
    }
}