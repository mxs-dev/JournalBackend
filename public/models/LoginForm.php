<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 12.12.2017
 * Time: 22:08
 */

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $roles;
    public $rememberMe = false;

    private $_user = false;

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function login () {

       if ($this->validate()) {
           return Yii::$app->user->login($this->getUserByUsername(),  $this->rememberMe ? 3600*24*30 : 0);
       }

       return false;
    }

    /**
     * @return bool | \app\models\User
     */
    public function getUser () {
        return $this->_user;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUsername();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * @return bool | \app\models\User
     */
    public function getUserByUsername()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

}