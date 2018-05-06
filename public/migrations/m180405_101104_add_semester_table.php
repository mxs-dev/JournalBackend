<?php

use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


class m180405_101104_add_semester_table extends Migration
{
    public static $tableName = 'semester';

    public function safeUp()
    {
        $this->createTable(static::$tableName, [
            'id'     => $this->primaryKey()->unsigned(),
            'yearId' => $this->integer(11)->unsigned(),
            'number' => $this->integer(3),

            'startDate' => $this->date(),
            'endDate'   => $this->date(),

            'createdAt' => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt' => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy' => $this->integer(11)->unsigned(),
            'updatedBy' => $this->integer(11)->unsigned()
        ]);
    }


    public function safeDown()
    {
        $this->dropTable(static::$tableName);
    }
}
