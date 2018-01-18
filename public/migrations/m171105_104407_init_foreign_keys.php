<?php

use yii\db\Migration;


class m171105_104407_init_foreign_keys extends Migration
{

    public function safeUp()
    {
        $this->addForeignKey(
            'fk-studying-user',
            'studying',
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        ); // `studying` - `user` (userId -> id) ;
        $this->addForeignKey(
            'fk-observe-user',
            'observe',
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        ); // `observe`  - `user` (userId -> id, childId -> id);
        $this->addForeignKey(
            'fk-teaches-user',
            'teaches',
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        ); // `teaches`  - `user` (userId -> id);
        $this->addForeignKey(
            'fk-grade-user',
            'grade',
            'userId',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        ); // `grade`    - `user` (userId -> id);
        $this->addForeignKey(
            'fk-teaches-subject',
            'teaches',
            'subjectId',
            'subject',
            'id',
            'CASCADE', 'CASCADE'
        ); // `teaches`  - `subject` (subjectId -> id);
        $this->addForeignKey(
            'fk-lesson-teaches',
            'lesson',
            'teachesId',
            'teaches',
            'id',
            'CASCADE', 'CASCADE'
        ); // `lesson`   - `teaches` (teachesId -> id);
        $this->addForeignKey(
            'fk-teaches-group',
            'teaches',
            'groupId',
            'group',
            'id',
            'CASCADE', 'CASCADE'
        ); // `teaches`  - `group` (groupId -> id);
        $this->addForeignKey(
            'fk-studying-group',
            'studying',
            'groupId',
            'group',
            'id',
            'CASCADE', 'CASCADE'
        ); // `studying` - `group` (groupId -> id);
        $this->addForeignKey(
            'fk-grade-lesson',
            'grade',
            'lessonId',
            'lesson',
            'id',
            'CASCADE', 'CASCADE'
        ); // `grade`    - `lesson` (lessonId -> id);
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk-studying-user', 'studying');
        $this->dropForeignKey('fk-observe-user',  'observe');
        $this->dropForeignKey('fk-teaches-user',  'teaches');
        $this->dropForeignKey('fk-grade-user',    'grade');
        $this->dropForeignKey('fk-teaches-subject', 'teaches');
        $this->dropForeignKey('fk-lesson-teaches',  'lesson');
        $this->dropForeignKey('fk-teaches-group',   'teaches');
        $this->dropForeignKey('fk-studying-group',  'studying');
        $this->dropForeignKey('fk-grade-lesson',    'grade');
    }
}
