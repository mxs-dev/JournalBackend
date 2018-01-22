<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 20.01.2018
 * Time: 14:24
 */

namespace app\models\forms\user;

use Yii;
use app\models\User;
use yii\base\Model;

class EditForm extends Model
{
    public $id;

    public $email;
    public $name;
    public $surname;
    public $patronymic;
    public $role;

    /** @var  User */
    private $_user;

    public function rules () {
        return [
            ['id', 'required'],
            ['email', 'email'],
            [['name', 'surname', 'patronymic'], 'string', 'max' => 100],
            ['role', 'safe'],
        ];
    }

    public function save () {

        if ($this->validate()) {
            $this->_user = User::findIdentity($this->id);

            $this->_user->email = $this->email ?? $this->_user->email;
            $this->_user->name = $this->name ?? $this->_user->name;
            $this->_user->surname = $this->surname ?? $this->_user->surname;
            $this->_user->patronymic = $this->patronymic ?? $this->_user->patronymic;

            if (Yii::$app->user->identity->role == User::ROLE_ADMIN){
                $this->_user->role = $this->role ?? $this->_user->role;
            }

            $this->_user->save();

            return true;
        }

        return false;
    }


    public function getUser () {
        return $this->_user;
    }
}