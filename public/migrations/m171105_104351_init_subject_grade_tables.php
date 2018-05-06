<?php

use yii\db\{ Schema, Migration };

class m171105_104351_init_subject_grade_tables extends Migration
{

    public function safeUp()
    {
        $this->createTable('group', [
            'id'     => $this->primaryKey()->unsigned(),
            'title'  => $this->string('50'),
            'course' => $this->integer(3),

            'createdAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createTable('subject', [
            'id'          => $this->primaryKey()->unsigned(),
            'title'       => $this->string(50),
            'description' => $this->text(),

            'createdAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);

        $this->createTable('lesson', [
            'id'          => $this->primaryKey()->unsigned(),
            'teachesId'   => $this->integer(11)->unsigned(),
            'date'        => $this->integer(11)->unsigned(),
            'type'        => $this->integer(3) ->unsigned(),
            'description' => $this->string(255),

            'createdAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);
        $this->createIndex('idx-lesson', 'lesson', 'teachesId');

        $this->createTable('grade', [
            'id'       => $this->primaryKey()->unsigned(),
            'userId'   => $this->integer(11)->unsigned(),
            'lessonId' => $this->integer(11)->unsigned(),

            'attendance' => $this->boolean()->defaultValue(true),
            'value'      => $this->integer(10)->unsigned(),

            'createdAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),
        ]);
        $this->createIndex('idx-grade', 'grade', ['userId', 'lessonId', 'attendance']);
    }


    public function safeDown()
    {
        $this->dropTable('group');
        $this->dropTable('lessons');
        $this->dropTable('subject');
        $this->dropTable('grade');
    }
}
