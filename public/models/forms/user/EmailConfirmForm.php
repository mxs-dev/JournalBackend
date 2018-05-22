<?php
namespace app\models\forms\user;

use Yii;
use app\models\User;
use yii\base\Model;


class EmailConfirmForm extends Model
{
    /** @var string */
    public $newPassword;

    /** @var string */
    public $emailConfirmToken;


    /** @var User   */
    private $_user;


    public function rules () {
        return [
            [ ['emailConfirmToken', 'newPassword'], 'required'],
            [ 'emailConfirmToken', 'checkToken']
        ];
    }


    public function checkToken ($attribute, $params) {
        if (!$this->hasErrors()) {
            $this->_user = User::findByEmailConfirmToken($this->emailConfirmToken);

            if (!empty($this->_user) && $this->_user->isEmailConfirmTokenNotExpired($this->emailConfirmToken)) {
                return true;
            }
        }

        $this->addError($attribute, Yii::t('app', "Ошибка. Обратитесь к администратору."));
        return false;
    }


    public function getUser () {
        return $this->_user;
    }


    public function confirm () {
        if ($this->validate()) {
            $this->_user -> status = User::STATUS_ACTIVE;
            $this->_user -> emailConfirmToken = null;
            $this->_user -> setPassword($this->newPassword);
            $this->_user -> save();

            return true;
        }

        return false;
    }
}