<?php

use app\models\User;
use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;

class m171105_103511_init_user_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('user', [
            'id'       => $this->primaryKey()->unsigned(),
            'email'    => $this->string(255),

            'name'       => $this->string(100)->notNull(),
            'surname'    => $this->string(100)->notNull(),
            'patronymic' => $this->string(100)->notNull(),

            'role'   => $this->integer(11)->defaultValue(10),
            'status' => $this->integer(3) ->defaultValue(1),

            'passwordHash'       => $this->string(50)->notNull(),
            'passwordResetToken' => $this->string(255),
            'emailConfirmToken'  => $this->string(255),

            'authKey'     => $this->string(255),
            'accessToken' => $this->string(100),
            'accessTokenExpirationDate' => Schema::TYPE_TIMESTAMP . ' NULL DEFAULT NULL',


            'createdAt'   => Schema::TYPE_TIMESTAMP,
            'updatedAt'   => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),

            'lastLoginIp' => $this->string(20),
            'lastLoginAt' => Schema::TYPE_TIMESTAMP . ' NULL DEFAULT NULL',
        ]);

        $this->createIndex('idx_user', 'user', ['email', 'authKey', 'passwordHash', 'status', 'role']);
        $this->createIndex('idx_user-name', 'user', ['name', 'surname', 'patronymic']);

        $this->createBasicUserRecords();
    }


    public function safeDown()
    {
        $this->dropIndex('idx_user', 'user');
        $this->dropIndex('idx_user-name', 'user');

        $this->dropTable('user');
    }


    protected function createBasicUserRecords () {
        $this->batchInsert('user',
            ['id', 'email', 'role', 'status', 'name', 'surname', 'patronymic', 'passwordHash', 'createdAt', 'createdBy'],
            [
                ['1', 'admin@a.b',   User::ROLE_ADMIN,     User::STATUS_ACTIVE, 'Admin', 'Admin', 'Admin',       Yii::$app->security->generatePasswordHash('admin'),   new Expression('NOW()'), '1'],
                ['2', 'moder@a.b',   User::ROLE_MODER,     User::STATUS_ACTIVE, 'Moder', 'Moder', 'Moder',       Yii::$app->security->generatePasswordHash('moder'),   new Expression('NOW()'), '1'],
                ['3', 'student@a.b', User::ROLE_STUDENT,   User::STATUS_ACTIVE, 'Student', 'Student', 'Student', Yii::$app->security->generatePasswordHash('student'), new Expression('NOW()'), '1'],
                ['4', 'parent@a.b',  User::ROLE_PARENT,    User::STATUS_ACTIVE, 'Parent', 'Parent', 'Parent',    Yii::$app->security->generatePasswordHash('parent'),  new Expression('NOW()'), '1']
            ]
        );
    }
}
