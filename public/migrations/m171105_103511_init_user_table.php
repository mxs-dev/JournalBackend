<?php

use yii\db\Schema;
use yii\db\Migration;

class m171105_103511_init_user_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('user', [
            'id'       => $this->primaryKey()->unsigned(),
            'username' => $this->string(50)->notNull(),
            'email'    => $this->string(255),

            'name'       => $this->string(100)->notNull(),
            'surname'    => $this->string(100)->notNull(),
            'patronymic' => $this->string(100)->notNull(),

            'role'   => $this->integer(11)->defaultValue(10),
            'status' => $this->integer(3) ->defaultValue(1),

            'passwordHash'       => $this->string(50)->notNull(),
            'passwordResetToken' => $this->string(255),
            'unconfirmedEmail'   => $this->string(255),

            'authKey'     => $this->string(255),
            'accessToken' => $this->string(100),
            'accessTokenExpirationDate' => Schema::TYPE_TIMESTAMP . ' NULL DEFAULT NULL',


            'createdAt'   => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            'updatedAt'   => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),

            'lastLoginIp' => $this->string(20),
            'lastLoginAt' => Schema::TYPE_TIMESTAMP . ' NULL DEFAULT NULL',
        ]);

        $this->createIndex('idx_user', 'user', ['username', 'authKey', 'passwordHash', 'status', 'role']);
        $this->createIndex('idx_user-name', 'user', ['name', 'surname', 'patronymic']);
    }


    public function safeDown()
    {
        $this->dropIndex('idx_user', 'user');
        $this->dropIndex('idx_user-name', 'user');

        $this->dropTable('user');
    }
}
