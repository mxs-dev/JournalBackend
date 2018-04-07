<?php
use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


/**
 * Class m180405_101129_add_foreign_keys
 */
class m180405_101129_add_foreign_keys extends Migration
{
    public function safeUp()
    {
        $this->addForeignKey(
            'fk_semester-year',
            'semester',
            'yearId',
            'academic_year',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_semester-year', 'semester');
    }
}
