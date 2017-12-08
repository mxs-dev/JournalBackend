<?php

use yii\db\Migration;


class m171105_104329_init_relations_tables extends Migration
{

    public function safeUp()
    {
        $this->createTable('teaches', [
            'id'        => $this->primaryKey(11)->unsigned(),
            'userId'    => $this->integer(11)->unsigned(),
            'subjectId' => $this->integer(11)->unsigned(),
            'groupId'   => $this->integer(11)->unsigned(),

            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned()
        ]);
        $this->createIndex('idx-teaches_userId,subjectId', 'teaches', ['userId', 'subjectId', 'groupId']);

        $this->createTable('studying', [
            'id'        => $this->primaryKey(11)->unsigned(),
            'userId'    => $this->integer(11)->unsigned(),
            'groupId'   => $this->integer(11)->unsigned(),

            'isActive'  => $this->boolean()->defaultValue(true),

            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned(),
        ]);
        $this->createIndex('idx-studying_userId, groupId', 'studying', ['userId', 'groupId']);

        $this->createTable('observe', [
            'id'        => $this->primaryKey(11)->unsigned(),
            'userId'    => $this->integer(11)->unsigned(),
            'childId'   => $this->integer(11)->unsigned(),
            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned(),
        ]);
    }


    public function safeDown()
    {
        $this->dropTable('teaches');
        $this->dropTable('studying');
        $this->dropTable('observe');
    }
}
