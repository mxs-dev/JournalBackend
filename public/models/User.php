<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use Firebase\JWT\JWT;
use yii\rbac\Permission;
use yii\web\Request as WebRequest;

/**
 * Class User
 *
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $name
 * @property string  $surname
 * @property string  $patronymic
 * @property integer $role
 * @property integer $status
 * @property string  $passwordHash
 * @property string  $passwordResetToken
 * @property string  $unconfirmedEmail
 * @property string  $authKey
 * @property string  $accessToken
 * @property integer $accessTokenExpirationDate
 * @property integer $createdAt
 * @property integer $createdBy
 * @property integer $updatedAt
 * @property integer $updatedBy
 * @property integer $lastLoginAt
 * @property string  $lastLoginIp
 *
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

    const TOKEN_ENCRYPTING_ALG = 'HS256';

    /** @var  string $accessToken to store JWT*/
    public $accessToken;

    /** @var  array $permissions to store list of permissions */
    public $permissions;

    /** @var  array  JWT token*/
    protected static $decodedToken;


    public static function tableName() {
        return 'user';
    }


    public static function findIdentity ($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }


    public static function findByUsername ($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds User by decoded AccessToken
     * @param string $accessToken
     * @param null $type
     * @return bool|null|\yii\web\IdentityInterface|static
     */
    public static function findIdentityByAccessToken ($accessToken, $type = null) {
        $secret = static::getJWTSecretCode();

        try{
            $decoded = JWT::decode($accessToken, $secret, [static::TOKEN_ENCRYPTING_ALG]);
        } catch (\Exception $e) {
            return false;
        }

        static::$decodedToken = (array) $decoded;

        if (!isset(static::$decodedToken['jti'])){
            return false;
        }

        $id = static::$decodedToken['jti'];
        return static::findIdentity($id);
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

    /**
     * Finds user by password reset token
     * @param $token
     * @return null|static
     */
    public function findByPasswordResetToken ($token) {
        if ( !static::isPasswordResetTokenNotExpired($token) ){
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Checks that password reset token is not expired
     * @param $token
     * @return bool
     */
    public function isPasswordResetTokenNotExpired ($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }



    /** returns JWT secret key */
    public static function getJWTSecretCode () {
        return \Yii::$app->params['jwtSecretCode'];
    }

    /**
     * Creates a custom JWT with user model id set in it
     * @return array encoded JWT
     */
    public function getJWT () {
        // Collect all the data
        $secret      = static::getJWTSecretCode();
        $currentTime = time();
        $expire      = $currentTime + 86400; // 1 day
        $request     = \Yii::$app->request;
        $hostInfo    = '';
        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }

        // Merge token with presets not to miss any params in custom
        // configuration
        $token = array_merge([
            'jti' => $this->getId(),    // JSON Token ID: A unique string, could be used to validate a token, but goes against not having a centralized issuer authority.
            'iat' => $currentTime,      // Issued at: timestamp of token issuing.
            'iss' => $hostInfo,         // Issuer: A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
            'aud' => $hostInfo,
            'nbf' => $currentTime,      // Not Before: Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid 10 seconds
            'exp' => $expire,           // Expire: Timestamp of when the token should cease to be valid. Should be greater than iat and nbf. In this case, the token will expire 60 seconds after being issued.
            'data' => [
                'username'      =>  $this->username,
                //'roleLabel'     =>  $this->getRoleLabel(),
                'lastLoginAt'   =>  $this->lastLoginAt,
            ]
        ], []);

        return [JWT::encode($token, $secret, static::TOKEN_ENCRYPTING_ALG), $token];
    }

    /**
     * Generate access token
     *  This function will be called every on request to refresh access token.
     *
     * @param bool $forceRegenerate whether regenerate access token even if not expired
     *
     * @return bool whether the access token is generated or not
     */
    public function generateAccessTokenAfterUpdatingUserInfo ($forceRegenerate = false) {
        $this->lastLoginIp = \Yii::$app->request->userIP;
        $this->lastLoginAt = new Expression("NOW()");

        if ($forceRegenerate
            || $this->accessTokenExpirationDate == null
            || (time() > strtotime($this->accessTokenExpirationDate)))
        {
            $this->generateAccessToken();
        }

        $this->save(false);
        return true;
    }

    public function generateAccessToken () {

        $tokens = $this->getJWT();
        $this->accessToken= $tokens[0];
        $this->accessTokenExpirationDate = date("Y-m-d H:i:s", $tokens[1]['exp']);
    }



}
