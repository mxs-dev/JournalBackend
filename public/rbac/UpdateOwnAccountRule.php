<?php
namespace app\rbac;

use Yii;
use yii\rbac\{ Item, Rule };

use app\models\User;


class UpdateOwnAccountRule extends Rule
{
    public $name = 'updateOwnAccount';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params ['accountId' = $userIdToUpdate]
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        if (Yii::$app->user->can(User::ROLE_MODER)){
            return true;
        }

        if ($user instanceof User){
            return $user->id == $params['accountId'];
        } else {
            return $user == $params['accountId'];
        }
    }
}