<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 12.12.2017
 * Time: 22:08
 */

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

class LoginForm extends Model
{
    /** @var string  */
    public $email;
    /** @var  string */
    public $password;

    /**
     * @var User
     */
    private $_user = false;


    public function rules()
    {
        return [
            [
                ['email', 'password'],
                'required',
                'message' => Yii::t("app", "Field cannot be blank")
            ],
            [ 'email', 'email' ],
            [ 'password', 'validatePassword' ],
        ];
    }


    public function login () {

       if ($this->validate()) {
           return Yii::$app->user->login( $this->getUserByEmail(), 0);
       }

       return false;
    }

    /**
     * @return bool | \app\models\User
     */
    public function getUser () {
        return $this->_user;
    }


    public function getUserByEmail () {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }


    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByEmail();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Username or password is incorrect'));
            }
        }
    }
}