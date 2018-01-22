<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 15.01.2018
 * Time: 8:49
 */

namespace app\models\forms\user;

use Yii;
use yii\base\Model;
use app\models\User;

class SignUpForm extends Model
{
    private $_user;

    public $email;

    public $name;
    public $surname;
    public $patronymic;

    public $role    = User::ROLE_STUDENT;
    private $status = User::STATUS_UNCONFIRMED;


    public function rules () {
        return [
            [
                ['email', 'name', 'surname', 'patronymic'],
                'required',
                'message' => Yii::t("app", Yii::t('app', "Field cannot be blank"))
            ],
            [
                'email', 'string', 'max' => 255
            ],
            [
                ['name', 'surname', 'patronymic'], 'string', 'max' => 100
            ],
            ['email', 'email'],
            ['role', 'safe'],
        ];
    }


    public function save () :bool {
        if ($this->validate()){
            $password = $this->generatePassword();

            $user = new User([
                'email'    => $this->email,
                'name'     => $this->name,
                'surname'  => $this->surname,
                'patronymic' => $this->patronymic,

                'role'   => $this->role,
                'status' => $this->status,

                'emailConfirmToken' => $this->generateEmailConfirmToken(),
                'passwordHash'      => Yii::$app->security->generatePasswordHash($password)
            ]);

            try {
                $user->save();
            } catch (\Exception $e) {
                throw $e;
            }


            $this->_user = $user;

            $this->sendEmailToUser();

            return true;
        }

        return false;
    }


    public function getUser () :User {
        return $this->_user;
    }



    protected function sendEmailToUser () {
        //TODO: create email sending logic.
    }


    protected function generatePassword () :string {

        if (YII_DEBUG) {
            return 'admin';
        }

        return Yii::$app->security->generateRandomString(6);
    }


    protected function generateEmailConfirmToken () :string {
        return Yii::$app->security->generateRandomString(100);
    }

}