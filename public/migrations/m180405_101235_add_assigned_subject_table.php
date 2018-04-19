<?php

use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


class m180405_101235_add_assigned_subject_table extends Migration
{
    public static $tableName = 'assigned_subject';
    public static $indexName = 'idx-assigned_subject';
    public static $fk_userTableName    = 'fk-assigned_subject-user';
    public static $fk_subjectTableName = 'fk-assigned_subject-subject';

    public function safeUp()
    {
        $this->createTable(static::$tableName,[
            'id'        => $this->primaryKey()->unsigned(),
            'userId'    => $this->integer(11)->unsigned(),
            'subjectId' => $this->integer(11)->unsigned(),

            'createdAt'   => Schema::TYPE_TIMESTAMP,
            'updatedAt'   => Schema::TYPE_TIMESTAMP,
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createIndex(
            static::$indexName,
            static::$tableName,
            ['id', 'userId', 'subjectId']
        );

        $this->addForeignKey(
            static::$fk_userTableName,
            static::$tableName,
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            static::$fk_subjectTableName,
            static::$tableName,
            'subjectId',
            'subject',
            'id',
            'CASCADE', 'CASCADE'
        );
    }


    public function safeDown()
    {
        $this->dropForeignKey(static::$fk_userTableName,    static::$tableName);
        $this->dropForeignKey(static::$fk_subjectTableName, static::$tableName);
        $this->dropTable(static::$tableName);
    }

}
