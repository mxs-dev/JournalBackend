<?php

use yii\db\Migration;


class m180416_111302_alter_tables extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            'teaches',
            'semesterId',
            $this->integer(11)->unsigned()
        );

        $this->addForeignKey(
            'fk-teaches-semester',
            'teaches',
            'semesterId',
            'semester',
            'id'
        );
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk-teaches-semester', 'teaches');
        $this->dropColumn('teaches', 'semesterId');
    }
}
