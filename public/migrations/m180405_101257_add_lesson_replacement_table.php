<?php

use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


class m180405_101257_add_lesson_replacement_table extends Migration
{
    public static $tableName = 'lesson_replacement';
    public static $indexName = 'idx-lesson_replacement';
    public static $fkUser   = 'fk-lesson_replacement-user';
    public static $fkLesson = 'fk-lesson_replacement-lesson';


    public function safeUp()
    {
        $this->createTable(static::$tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'lessonId' => $this->integer(11)->unsigned(),
            'userId' => $this->integer(11)->unsigned(),

            'createdAt'   => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            'updatedAt'   => Schema::TYPE_TIMESTAMP,
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createIndex(
            static::$indexName,
            static::$tableName,
            ['id', 'lessonId', 'userId']
        );

        $this->addForeignKey(
            static::$fkUser,
            static::$tableName,
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            static::$fkLesson,
            static::$tableName,
            'lessonId',
            'lesson',
            'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(static::$fkUser, static::$tableName);
        $this->dropForeignKey(static::$fkLesson, static::$tableName);
        $this->dropTable(static::$tableName);
    }

}
