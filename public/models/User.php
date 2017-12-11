<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class User
 * @package app\models
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_STUDENT = 10;
    const ROLE_PARENT  = 20;
    const ROLE_TEACHER = 50;
    const ROLE_ADMIN   = 99;

    const STATUS_ACTIVE   =  1;
    const STATUS_DELETED  = -1;
    const STATUS_DISABLED =  0;

    /** @var  string $accessToken to store JWT*/
    public $accessToken;

    /** @var  array $permissions to store list of permissions */
    public $permissions;


    public static function tableName() {
        return 'user';
    }

    public static function findIdentity ($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsername ($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }



    public function attributeLabels () {
        return [
            'username',
            'email',
            ''
        ];
    }



    public function getId () {
        return $this->id;
    }


    public function getAuthKey () {
        return $this->authKey;
    }


    public function validateAuthKey ($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param $password
     * @return bool
     */
    public function validatePassword ($password) {
        return \Yii::$app->security->validatePassword($password, $this->passwordHash);
    }


    public function setPassword ($password) {
        $this->passwordHash = \Yii::$app->security->generatePasswordHash($password);
    }


    public function generateAuthKey () {
        $this->authKey = \Yii::$app->security->generateRandomString();
    }

    /**
     *  Generates a new password reset token(with creation date)
     */
    public function generatePasswordResetToken () {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }


    public function removePasswordResetToken () {
        $this->passwordResetToken = null;
    }


    public function isPasswordResetTokenValid ($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }
}
