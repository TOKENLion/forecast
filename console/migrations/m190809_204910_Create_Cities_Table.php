<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%Cities}}`.
 */
class m190809_204910_Create_Cities_Table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull()
        ]);

//        $this->addForeignKey(
//            'FK_city_country',
//            '{{%cities}}',
//            'country_id',
//            '{{%countries}}',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cities}}');
    }
}
