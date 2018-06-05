<?php

use yii\db\Migration;


class m180605_114917_alter_tables_teaches_lesson extends Migration
{
    public function safeUp()
    {
        $this->addColumn('teaches', 'hoursCount', $this->integer(11)->notNull());
        $this->addColumn('lesson', 'weight', $this->float()->defaultValue(1));
    }


    public function safeDown()
    {
    }
}
