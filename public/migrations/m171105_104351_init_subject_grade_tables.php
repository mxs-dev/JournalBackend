<?php

use yii\db\Migration;


class m171105_104351_init_subject_grade_tables extends Migration
{

    public function safeUp()
    {
        $this->createTable('group', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string('50'),
            'course' => $this->integer(4),
            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned(),
        ]);

        $this->createTable('lessons', [
            'id'        => $this->primaryKey()->unsigned(),
            'teachesId' => $this->integer(11)->unsigned(),
            'date'      => $this->integer(11)->unsigned(),
            'type'      => $this->string(10)->defaultValue('lecture'),
            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned(),

        ]);

        $this->createTable('subject', [
            'id'          => $this->primaryKey()->unsigned(),
            'title'       => $this->string(50),
            'description' => $this->text(),

            'createdAt'   => $this->integer(11)->unsigned(),
            'createdBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createTable('grade', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer(11)->unsigned(),
            'lessonId' => $this->integer(11)->unsigned(),

            'was' => $this->boolean()->defaultValue(true),
            'grade' => $this->integer(10)->unsigned(),

            'createdAt' => $this->integer(11)->unsigned(),
            'createdBy' => $this->integer(11)->unsigned(),
        ]);
    }


    public function safeDown()
    {
        $this->dropTable('group');
        $this->dropTable('lessons');
        $this->dropTable('subject');
        $this->dropTable('grade');
    }
}
