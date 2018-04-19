<?php

use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


class m180405_101046_add_academic_year_table extends Migration
{
    public static $tableName = 'academic_year';

    public function safeUp()
    {
        $this->createTable(static::$tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string('10'),

            'startDate' => Schema::TYPE_TIMESTAMP,
            'endDate'   => Schema::TYPE_TIMESTAMP,

            'createdAt' => Schema::TYPE_TIMESTAMP,
            'updatedAt' => Schema::TYPE_TIMESTAMP,
            'createdBy' => $this->integer(11)->unsigned(),
            'updatedBy' => $this->integer(11)->unsigned()
        ]);

        $this->createIndex(
            'idx-academic_year',
            static::$tableName,
            ['title', 'startDate', 'endDate']
        );
    }


    public function safeDown()
    {
        $this->dropIndex('idx-academic_year', static::$tableName);
        $this->dropTable(static::$tableName);
    }
}
