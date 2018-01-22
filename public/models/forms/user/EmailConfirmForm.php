<?php
namespace app\models\forms\user;

use Yii;
use app\models\User;
use yii\base\Model;


class EmailConfirmForm extends Model
{
    /** @var int    */
    public $id;
    /** @var string */
    public $emailConfirmToken;


    /** @var User   */
    private $_user;


    public function rules () {
        return [
            [
                ['id', 'emailConfirmToken'],
                'required'
            ],
            [ 'emailConfirmToken', 'checkToken']
        ];
    }


    public function attributeLabels () {
        return [
            'id' => Yii::t('app', 'Id'),
            'emailConfirmToken' => Yii::t('app', 'Email confirmation token')
        ];
    }


    public function checkToken ($attribute, $params) {

        if (!$this->hasErrors()) {
            $this->_user = User::findOne(['id' => $this->id]);

            if (!empty($this->_user) && $this->emailConfirmToken == $this->_user->emailConfirmToken) {
                return true;
            }
        }

        $this->addError($attribute, Yii::t('app', "Token is not valid"));
        return false;
    }


    public function getUser () {
        return $this->_user;
    }


    public function confirm () {
        if ($this->validate()) {
            $this->_user -> status = User::STATUS_ACTIVE;
            $this->_user -> emailConfirmToken = null;
            $this->_user -> save();

            return true;
        }

        return false;
    }
}