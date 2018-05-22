<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 20.01.2018
 * Time: 16:53
 */

namespace app\commands;

use yii\console\Controller;

use app\models\User;
use app\rbac\UpdateOwnAccountRule;

class RbacController extends Controller
{

    /**
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionInit () {
        $authManager = \Yii::$app->authManager;

        $student = $authManager -> createRole(User::ROLE_STUDENT);
        $teacher = $authManager -> createRole(User::ROLE_TEACHER);
        $parent  = $authManager -> createrole(User::ROLE_PARENT);
        $moder   = $authManager -> createRole(User::ROLE_MODER);
        $admin   = $authManager -> createRole(User::ROLE_ADMIN);


        $authManager->add($admin);
        $authManager->add($moder);
        $authManager->add($teacher);
        $authManager->add($student);
        $authManager->add($parent);

        $authManager->addChild($admin, $moder);
        $authManager->addChild($moder, $teacher);
        $authManager->addChild($teacher, $student);
        $authManager->addChild($student, $parent);



        // Assign roles to users
        $users = User::find()->all();

        /** @var User $user */
        foreach ($users as $user) {
            $roleToAssign = null;

            switch ($user->role) {
                case User::ROLE_ADMIN:
                    $roleToAssign = $admin;
                    break;
                case User::ROLE_MODER:
                    $roleToAssign = $moder;
                    break;
                case User::ROLE_TEACHER:
                    $roleToAssign = $teacher;
                    break;
                case User::ROLE_STUDENT:
                    $roleToAssign = $student;
                    break;
                default: $roleToAssign = $parent;
            }

            $authManager->assign($roleToAssign, $user->id);
        }

        echo "success\n";

        return true;
    }


    /**
     * @throws \yii\base\Exception
     */
    public function actionCreateRules () {
        $this->createUpdateOwnAccountRule();

        echo "success\n";
        return true;
    }


    /**
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    protected function createUpdateOwnAccountRule () {
        $authManager = \Yii::$app->authManager;

        $updateOwnAccountRule = new UpdateOwnAccountRule();
        $authManager->add($updateOwnAccountRule);

        $updateOwnAccount = $authManager->createPermission('updateOwnAccount');
        $updateOwnAccount -> ruleName = $updateOwnAccountRule->name;
        $authManager -> add($updateOwnAccount);


        $parent = $authManager->getRole(User::ROLE_PARENT);
        $authManager -> addChild($parent, $updateOwnAccount);
    }
}