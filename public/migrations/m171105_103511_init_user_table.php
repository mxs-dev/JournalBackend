<?php

use yii\db\Schema;
use yii\db\Migration;

class m171105_103511_init_user_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('user', [
            'id'        => $this->primaryKey()->unsigned(),
            'username'  => $this->string(50)->notNull(),
            'password'  => $this->string(50)->notNull(),
            'email'     => $this->string(50),

            'name'      => $this->string(100)->notNull(),
            'surname'   => $this->string(100)->notNull(),
            'patronymic'=> $this->string(100)->notNull(),
            'gender'    => $this->string(10)->defaultValue('man'),
            'type'      => $this->string(10)->defaultValue('student'),

            'authKey'     => $this->string(100),
            'accessToken' => $this->string(100),
            'createdAt'   => $this->integer(11)->unsigned(),
            'createdBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createIndex('idx_user-username', 'user', ['username']);
        $this->createIndex('idx_user-name,surname', 'user', ['name', 'surname']);
    }

    public function safeDown()
    {
        $this->dropTable('user');
        $this->dropIndex('idx_user-username', 'user');
        $this->dropIndex('idx_user-name,surname', 'user');
    }
}
