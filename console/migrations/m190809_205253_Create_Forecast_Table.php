<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%Forecast}}`.
 */
class m190809_205253_Create_Forecast_Table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%forecast}}', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->notNull(),
            'temperature' => $this->float()->notNull(),
            'when_created' => $this->text(),
        ]);

//        $this->addForeignKey(
//            'FK_forecast_city',
//            '{{%forecast}}',
//            'city_id',
//            '{{%cities}}',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
//        ALTER TABLE public.forecast ALTER COLUMN temperature TYPE real USING temperature::real;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%forecast}}');
    }
}
